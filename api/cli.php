<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

$di = new CliDI();

//print __FILE__ ;

define('APP_PATH', realpath( dirname(__FILE__) . '/app'));

//print APP_PATH;

$config = include APP_PATH . '/config/config.php';
include APP_PATH . '/config/loader.php';

$console = new ConsoleApp();

$console->setDI($di);

/**
 * Process the console arguments
*/
//$di = new CliDI();
$di['db'] = function () use ($config) {
    return new DbAdapter(
		[
        "host"     => $config->db->host,
        "username" => $config->db->username,
        "password" => $config->db->password,
        "dbname"   => $config->db->dbname,
		"options"  => [
						\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
					]
        ]
	);
};

$di['config'] = $config ;

$console = new ConsoleApp();

$console->setDI($di);

$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    // Do Phalcon related stuff here
    // ..
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
}

    