<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})
->bind('homepage');

$app->get('/repositories', function () use ($app) {
    return $app['twig']->render('repositories.html', array('repositories' => $app['github.manager']->getAllRepositoriesWithBranch()));
})
->bind('repositories');

$app->get('/create-prototype', function () use ($app) {
    return $app['twig']->render('create.html', array('repositories' => $app['github.manager']->getAllRepositoriesWithBranch()));
})
->bind('create-prototype');


$app->post('/launch-prototype', function () use ($app) {
    var_dump($app['request']->request->all());
    die();
    return $app['twig']->render('launch.html', array());
})
->bind('launch-prototype');


# Include or render for twig

$app->get('/_status', function () use ($app) {
    return $app['twig']->render('_status.html', array('vms' => $app['vm.manager']->loadAll()));
})
->bind('_status');

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});