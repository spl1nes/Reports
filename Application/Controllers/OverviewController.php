<?php

namespace Reports\Application\Controllers;

use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\Local\File;

class OverviewController extends DashboardController
{
    public function showOverview(RequestAbstract $request, ResponseAbstract $response)
    {
        $view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/overview');

	$dir = new Directory(__DIR__ . '/../Templates/reports');
	$files = [];	

	foreach($dir as $file) {
		$files[] = ['url' => str_replace([' ', '_'], '', $file->getName()), 'name' => str_replace('_', ' ', $file->getName())];
	}

	$view->addData('reportList', $files);

        return $view;
    }

}