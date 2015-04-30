<?php

if ('cli-server' === php_sapi_name()) {
    $filename = dirname(__FILE__).preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);

    // If it is a file, just return false.
    if (is_file($filename)) {
        return false;
    }
}

$app = require_once __DIR__.'/../src/bootstrap.php';
$app->run();
