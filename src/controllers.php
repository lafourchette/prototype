<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})
->bind('homepage');

$app->get('/login', function () use ($app) {
    
    $username = $app['request']->server->get('PHP_AUTH_USER', false);
    $password = $app['request']->server->get('PHP_AUTH_PW', false);
    
    if($username && $password)
    {
        $userManager = $app['user.manager'];

        //Retrieve user information
        $ldapUser = $app['ldap.manager']->getUserInfo($username);

            if(null !== $ldapUser)
            {

            $user = $userManager->loadOneBy(array('username' => $ldapUser->getUsername()));

            if(null === $user){
                $userManager->save($ldapUser);
                $user = $userManager->loadOneBy(array('username' => $ldapUser->getUsername()));
            }

            if(null !== $user)
            {
                $isAuthenticated = $app['ldap.manager']->bind($user->getDn(), $password);
                if($isAuthenticated)
                {
                    $app['session']->set('isAuthenticated', $isAuthenticated);
                    $app['session']->set('user', $user);
                    return $app->redirect($app['url_generator']->generate('homepage'));
                }
            }
        }
    }
    
    return $app['login.basic_login_response'];
})
->bind('login');

$app->get('/create-prototype', function () use ($app) {
    var_dump($app['session']->get('user'));
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
    return $app['twig']->render('_status.html', array('vms' => $app['vm.manager']->loadVm()));
})
->bind('_status');

$app->get('/logout', function () use ($app) {
        $app['session']->set('isAuthenticated', false);
        $app['session']->set('user', null);
        return $app['login.basic_login_response'];
    })->bind('logout');

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});


// check login
$app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {
    $request = $event->getRequest();
    

    if ($request->get('_route') === '_profiler') { 
       return;
    }

    if ($request->get('_route') === 'login')
    {
        return;
    }
    
    if (!$app['session']->get('isAuthenticated')) {
        $ret = $app->redirect($app['url_generator']->generate('login'));
    } else {
        $ret = null;
    }
    
    if ($ret instanceof Response) {
        $event->setResponse($ret);
    }
}, 0);