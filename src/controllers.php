<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use LaFourchette\Entity\Vm;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})
->bind('homepage');

$app->get('/login', function () use ($app) {
    
    $username = $app['request']->server->get('PHP_AUTH_USER', false);
    $password = $app['request']->server->get('PHP_AUTH_PW', false);
    
    if($username && $password) {
        $userManager = $app['user.manager'];

        //Retrieve user information
        $ldapUser = $app['ldap.manager']->getUserInfo($username);

        if(null !== $ldapUser) {
            $user = $userManager->getOrCreate($ldapUser);

            if(null !== $user) {
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
    $params = array();
    $params['repositories'] = $app['github.manager']->getAllRepositoriesWithBranch();
    $params['users'] = $app['ldap.manager']->listUsers();

    return $app['twig']->render('create.html', $params);
})
    ->bind('create-prototype');

$app->get('/force-expire-prototype/{idVm}', function ($idVm) use ($app) {
    $vmManager = $app['vm.manager'];
    /**
     * @var Vm $vm
     */
    $vm = $vmManager->load($idVm);

    if ($vm->getCreatedBy()->getIdUser() == $app['session']->get('user')->getIdUser()) {
        $vm->setExpiredDt(new \DateTime());
        $vm->setStatus(Vm::EXPIRED);
        $vmManager->flush($vm);

        $app[ 'session' ]->set( 'flash', array(
            'type'    =>'success',
            'short'   =>'The VM has expired',
            'ext'     =>'The given VM has been set to expired. Its slot will be free and could be reuse in some minutes by another person.',
        ) );
    } else {
        $app[ 'session' ]->set( 'flash', array(
            'type'    =>'error',
            'short'   =>'You have no rigth to do this.',
            'ext'     =>'You can only force the expiration of a VM that you have created.',
        ) );
    }

    return $app->redirect('/');
})
    ->bind('force-expire-prototype');

$app->get('/ask-more-prototype/{idVm}', function ($idVm) use ($app) {
    $vmManager = $app['vm.manager'];
    /**
     * @var Vm $vm
     */
    $vm = $vmManager->load($idVm);

    if ($vm->getCreatedBy()->getIdUser() == $app['session']->get('user')->getIdUser()) {
        $date = $vm->getExpiredDt();
        $date->add(new \DateInterval('PT2H'));

        /**
         * Force the clone because without it doctrine do not detect the change and so it do not update the db
         */
        $vm->setExpiredDt(clone $date);
        $vm->setStatus(Vm::STOPPED);
        $vmManager->save($vm);

        $app[ 'session' ]->set( 'flash', array(
            'type'    =>'success',
            'short'   =>'2 hours has been added for your VM',
            'ext'     =>'Please don\'t abuse of this feature. Do you really need as much time to test your stuff?',
        ) );
    } else {
        $app[ 'session' ]->set( 'flash', array(
            'type'    =>'error',
            'short'   =>'You have no rigth to do this.',
            'ext'     =>'You can only add time of a VM that you have created.',
        ) );
    }

    return $app->redirect('/');
})
    ->bind('ask-more-prototype');

$app->post('/launch-prototype', function () use ($app) {
    if(null === $projects = $app['request']->request->get('projects')) {
        throw new \Exception('The "projects" variable is missing');
    }

    $users = $app['request']->request->get('users');
    
    //Refactor this please...
    if($app['integ_availabibilty.checker']->check())
    {
        //Doctrine2 does not handle correctly
        $creator = $app['vm.creator'];
        /**
         * @var Vm $vm
         */
        $vm = $creator->create();

        $user = $app['user.manager']->getOrCreate($app['session']->get('user'));
        $vm->setCreatedBy($user);
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

        $userManager = $app['user.manager'];
        $userNotifyManager = $app['user_notify.manager'];

        foreach ($users as $userName) {
            $ldapUser = $app['ldap.manager']->getUserInfo($userName);
                $user = $userManager->getOrCreate($ldapUser);

                $userNotify = new \LaFourchette\Entity\UserNotify();
                $userNotify->setUser($user);
                $userNotify->setVm($vm);
                $userNotifyManager->save($userNotify);
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