<?php
$dispatch = $this->getData('export') ?? [];

foreach ($dispatch as $view) {
	if ($view instanceOf \Serializable) {
		echo $view->render();
	}
}

echo 'aaaaaaaaaaaaaaaaa';