<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('APP_PATH', realpath('..\app'));
try {
   
    $config = include APP_PATH . '\config\config.php';
    include APP_PATH . '\config\loader.php';
    include APP_PATH . '\config\services.php';
	
    $app = new Micro($di);
    
    include APP_PATH . '\..\app.php';

    $app->handle();

} catch (\Exception $e) {
    
	echo $e->getMessage();
}
