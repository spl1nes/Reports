<?php
namespace Reports\Application;

use phpOMS\ApplicationAbstract;
use phpOMS\Asset\AssetType;
use phpOMS\DataStorage\Cache\CachePool;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Database\DatabaseStatus;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Localization\Localization;
use phpOMS\Message\Http\Request;
use phpOMS\Message\Http\RequestStatus;
use phpOMS\Message\Http\Response;
use phpOMS\Model\Html\Head;
use phpOMS\Router\Router;
use phpOMS\Uri\Http;
use phpOMS\Views\View;
use phpOMS\Uri\UriFactory;

class WebApplication extends ApplicationAbstract
{
    public $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->run();
    }

    private function run()
    {
        set_exception_handler(['\phpOMS\UnhandledHandler', 'exceptionHandler']);
        set_error_handler(['\phpOMS\UnhandledHandler', 'errorHandler']);
        register_shutdown_function(['\phpOMS\UnhandledHandler', 'shutdownHandler']);
        mb_internal_encoding('UTF-8');
        date_default_timezone_set('Europe/Berlin');

        $this->cachePool = new CachePool();
        $this->cachePool->create('file', $this->config['cache']['file']);

        $request  = new Request(new Localization());
        $response = new Response(new Localization());
        $response->getL11n()->setLanguage('en');
        $request->getL11n()->setLanguage('en');
        $request->init();
        $request->getUri()->setRootPath($this->config['page']['root']);

        UriFactory::setupUriBuilder($request->getUri());

        $expire = new \DateTime('now');
        $expire->modify($this->config['cache']['http']['expire']);

        $response->getHeader()->set('x-xss-protection', '1; mode=block');
        $response->getHeader()->set('x-content-type-options', 'nosniff');
        $response->getHeader()->set('x-frame-options', 'SAMEORIGIN');
        $response->getHeader()->set('content-security-policy', 'script-src \'self\' \'unsafe-inline\' https://cdnjs.cloudflare.com; child-src \'self\'', true);
        $response->getHeader()->set('expires', $expire->format('D, d M Y H:i:s \G\M\T'));

        if ($this->config['page']['https']) {
            $response->getHeader()->set('strict-transport-security', 'max-age=31536000');
        }

        $response = $this->generateContent($request, $response);
        $response->getHeader()->push();

        echo $response->getBody();
    }

    private function generateContent(Request $request, Response $response)
    {
        $this->dbPool = new DatabasePool();
        $this->dbPool->create('sd', $this->config['db']['SD']);
        $this->dbPool->create('gdf', $this->config['db']['GDF']);
        $this->dbPool->create('lucanet', $this->config['db']['lucanet']);

        if (
            $this->dbPool->get('sd')->getStatus() !== DatabaseStatus::OK 
            || $this->dbPool->get('gdf')->getStatus() !== DatabaseStatus::OK 
            || $this->dbPool->get('lucanet')->getStatus() !== DatabaseStatus::OK
        ) {
            $dispatched   = [];
            $dispatched[] = new View($this, $request, $response);
            $dispatched[0]->setTemplate('/Reports/Application/Templates/Page/error');
            $response->setStatusCode(RequestStatus::R_503);
        } else {
            $this->router = new Router();
            $this->router->importFromFile(__DIR__ . '/Routes.php');

            $this->dispatcher = new Dispatcher($this);
            $dispatched       = $this->dispatcher->dispatch($this->router->route($request), $request, $response);
        }

        if (empty($dispatched)) {
            $dispatched[] = new View($this, $request, $response);
            $dispatched[0]->setTemplate('/Reports/Application/Templates/Page/error');
            $response->setStatusCode(RequestStatus::R_404);
        }

        if($request->getData('export') === null) {
            $pageView = new View($this, $request, $response);
            $head     = new Head();
            $baseUri  = $request->getUri()->getBase();

            $head->addAsset(AssetType::JS, $baseUri . 'Model/Message/DomAction.js');
            $pageView->setData('unit', $request->getData('u') ?? 'consolidated');
            $pageView->setData('head', $head);

            $title = 'Reports';
            foreach($dispatched as $view) {
                if(isset($view) && $view->getData('title') !== null) {
                    $title = $view->getData('title');
                }
            }

            $pageView->setData('title', $title);
            $pageView->setData('dispatch', $dispatched);
            $pageView->setTemplate('/Reports/Application/Templates/index');
            $response->set('Content', $pageView);
        } 

        return $response;
    }
}
