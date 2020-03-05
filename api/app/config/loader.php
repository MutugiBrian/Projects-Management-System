<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(
  [
    'Chemicals\Backend'      =>  APP_PATH . '/modules/backend/classes/',
	'PHPMailer\PHPMailer'    => APP_PATH . '/libraries/PHPMailer/src/',
  ]
);

$loader->registerDirs(
    [
        APP_PATH . '/tasks',
    ]
);

$loader->register();
