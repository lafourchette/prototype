<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

if ('cli-server' === php_sapi_name()) {
    $filename = dirname(__FILE__).preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);

    // If it is a file, just return false.
    if (is_file($filename)) {
        return false;
    }
}

$app = require __DIR__.'/../src/app.php';

if ($app['debug']) {
    error_reporting(-1);
    Symfony\Component\Debug\DebugClassLoader::enable();
    Symfony\Component\HttpKernel\Debug\ErrorHandler::register();
    if ('cli-server' !== php_sapi_name()) {
        Symfony\Component\HttpKernel\Debug\ExceptionHandler::register();
    }
}

require __DIR__.'/../src/controllers.php';
$app->run();
