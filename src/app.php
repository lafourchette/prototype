<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => array(__DIR__ . '/../templates'),
    'twig.options' => array('cache' => __DIR__ . '/../cache/twig'),
));

require __DIR__ . '/../src/services.php';

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addExtension(new \LaFourchette\Twig\Extensions\LaFourchettePrototypeExtension($app['integ_availabibilty.checker']));
    $twig->addGlobal('asset_version', $app['asset.version']);

    return $twig;
}));

$app->before( function() use ( $app ) {
    $flash = $app[ 'session' ]->get( 'flash' );
    $app[ 'session' ]->set( 'flash', null );

    if ( !empty( $flash ) )
    {
        $app[ 'twig' ]->addGlobal( 'flash', $flash );
    }
});

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../db/dev',
    ),
));

$app->register(new \Silex\Provider\SessionServiceProvider());

$app->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
    "orm.proxies_dir" => __DIR__ . '/../cache/doctrine/proxies',
    "orm.em.options" => array(
        "mappings" => array(
            array(
                "type" => "annotation",
                "use_simple_annotation_reader" => false,
                "namespace" => "LaFourchette\Entity",
                "path" => __DIR__ . "/src/LaFourchette/Entity",
            )
        ),
    ),
));

return $app;
