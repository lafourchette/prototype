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

$app->get('/create-prototype', function () use ($app) {
    return $app['twig']->render('create.html', array('repositories' => $app['github.manager']->getAllRepositoriesWithBranch()));
})
->bind('create-prototype');

$app->post('/launch-prototype', function () use ($app) {
    if(null === $projects = $app['request']->request->get('projects'))
    {
        throw new \Exception('The "projects" variable is missing');
    }
    
    //Refactor this please...
    if($app['integ_availabibilty.checker']->check())
    {
        //Doctrine2 does not handle correctly
        $creator = $app['vm.creator'];
        $vm = $creator->create();
        //Save the vm first
        $app['vm.manager']->save($vm);

        //Create a related object between project and vm
        foreach ($projects as $projectId => $branch) {
                $vmProjectCreator = $app['vm_project.creator'];
                $vmProjectCreator->addBranch($branch);
                $vmProjectCreator->addProject($app['project.manager']->load($projectId));
                $vmProjectCreator->addVm($vm);
                $vmProject = $vmProjectCreator->create();
                $app['vm_project.manager']->save($vmProject);
        }
    }
    
    return $app->redirect('/');
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