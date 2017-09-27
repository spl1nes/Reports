<h1>Report Overview</h1>

<ul>
<?php $reportList = $this->getData('reportList') ?? []; foreach($reportList as $report) : ?>
<li><a href="<?= \phpOMS\Uri\UriFactory::build('{/base}/report/' . $report['url']); ?>"><?= $report['name']; ?>
<?php endforeach; ?>
</ul>