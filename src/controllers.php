<?php

use LaFourchette\Entity\User;
use LaFourchette\Entity\Vm;
use LaFourchette\Logger\VmLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Process\Process;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})
->bind('homepage');

$app->get('/login', function () use ($app) {

    $username = $app['request']->server->get('PHP_AUTH_USER', false);
    $password = $app['request']->server->get('PHP_AUTH_PW', false);

    if (($username && $password) || $app['debug']) {

        $userManager = $app['user.manager'];

        //Retrieve user information
        $ldapUser = $app['ldap.manager']->getUserInfo($username);

        if (null !== $ldapUser) {
            $user = $userManager->getOrCreate($ldapUser);

            if (null !== $user) {
                $isAuthenticated = $app['ldap.manager']->bind($user->getDn(), $password);
                if ($isAuthenticated) {
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

$app->get('/users', function () use ($app) {
    return $app['twig']->render('users.html', array(
        'users' => $app['ldap.manager']->listUsers()
    ));
});

$app->get('/cc.xml', function () use ($app) {
    $exporter = $app['vm.cc.exporter'];

    return $exporter->export();
})
->bind('cc');

$app->get('/show-prototype/{idVm}', function ($idVm) use ($app) {
    return $app['twig']->render('show.html', array(
        'vm' => $app['vm.manager']->load($idVm),
        'users' => $app['user_notify.manager']->loadBy(array('vm' => $idVm))
    ));
})
->bind('show');

$app->get('/enlarge-version/{idVm}', function ($idVm) use ($app) {
    return $app['twig']->render('log.html', array(
        'vm' => $app['vm.manager']->load($idVm),
    ));
})
->bind('enlarge_version');

$app->get('/_ajax_log/{idVm}', function ($idVm) use ($app) {

    $filename = VmLogger::getLogFile($idVm);

    $lastModified = 0;
    if (file_exists($filename)) {
        $lastModified = filemtime($filename);
    }

    $data = array();
    $data['status'] = 0;

    //Test if the file has been mofidied since the last 5 min
    if ($lastModified >= strtotime($app['config']['log.max_time_before_logging'])) {
        $data['msg'] = $app['twig']->render('_ajax_log.html', array(
            'vm' => $app['vm.manager']->load($idVm),
        ));
    } else {
        $data['status'] = 1;
    }

    return new JsonResponse($data, 200);

})
->bind('_ajax_log');

$app->get('/create-prototype', function () use ($app) {
    $params = array();
    $params['integs'] = $app['integ.manager']->loadAllAvailable();
    $params['users'] = $app['ldap.manager']->listUsers();
    $params['vmActive'] = $app['vm.manager']->getActive();
    $params['vmToStart'] = $app['vm.manager']->getToStart();

    return $app['twig']->render('create.html', $params);
})
    ->bind('create-prototype');

$app->get('/force-start/{idVm}', function ($idVm) use ($app) {
    $vmManager = $app['vm.manager'];
    $vm = $vmManager->load($idVm);

    if ($vm->getCreatedBy()->getIdUser() == $app['session']->get('user')->getIdUser()) {
        $vm->setStatus(Vm::TO_START);
        $vmManager->flush($vm);

        $app[ 'session' ]->set('flash', array(
            'type'    =>'success',
            'short'   =>'The VM has restarted',
            'ext'     =>'The given VM has been set to start.',
        ));
    } else {
        $app[ 'session' ]->set('flash', array(
            'type'    =>'error',
            'short'   =>'You have no rigth to do this.',
            'ext'     =>'You can only force the expiration of a VM that you have created.',
        ));
    }

    return $app->redirect('/');

})
    ->bind('force-start');

$app->get('/force-expire-prototype/{idVm}', function ($idVm) use ($app) {
    $vmManager = $app['vm.manager'];
    $vm = $vmManager->load($idVm);

    if ($vm->getCreatedBy()->getIdUser() == $app['session']->get('user')->getIdUser()) {
        $vm->setExpiredDt(new \DateTime());
        $vm->setStatus(Vm::EXPIRED);
        $vmManager->flush($vm);

        $app[ 'session' ]->set('flash', array(
            'type'    =>'success',
            'short'   =>'The VM has expired',
            'ext'     =>'The given VM has been set to expired. Its slot will be free and could be reuse in some minutes by another person.',
        ));
    } else {
        $app[ 'session' ]->set('flash', array(
            'type'    =>'error',
            'short'   =>'You have no rigth to do this.',
            'ext'     =>'You can only force the expiration of a VM that you have created.',
        ));
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
        $date->add(new \DateInterval(sprintf('PT%dH', $app['config']['vm.expired_in_value'])));
        $day = $date->format('w');
        $short = sprintf('%s hours has been added for your VM', $app['config']['vm.expired_in_value']);
        if ($day == 0 || $day == 6) { // expire on weekend... well add 2 more days then.
            $date->add(new \DateInterval('P2D'));
            $short = 'Your VM will expire after the weekend';
        }
        /**
         * Force the clone because without it doctrine do not detect the change and so it do not update the db
         */
        $vm->setExpiredDt(clone $date);
        $vm->setStatus(Vm::RUNNING);
        $vmManager->save($vm);

        $app[ 'session' ]->set('flash', array(
            'type'    => 'success',
            'short'   => $short,
            'ext'     => 'Please don\'t abuse of this feature. Do you really need as much time to test your stuff?',
        ));
    } else {
        $app[ 'session' ]->set('flash', array(
            'type'    =>'error',
            'short'   =>'You have no rigth to do this.',
            'ext'     =>'You can only add time of a VM that you have created.',
        ));
    }

    return $app->redirect('/');
})
    ->bind('ask-more-prototype');

/**
 * Call on create-prototype page, actually does the creation
 */
$app->post('/launch-prototype', function () use ($app) {

    $users = $app['request']->request->get('users');

    //Refactor this please...
    if ($app['integ.manager']->hasAvailableInteg()) {
        //Doctrine2 does not handle correctly
        $creator = $app['vm.creator'];
        //Get Integ Parameter
        $integ = $app['integ.manager']->load($app['request']->request->get('integ'));
        $vm = $creator->create();

        $user = $app['user.manager']->getOrCreate($app['session']->get('user'));
        $vm->setCreatedBy($user);
        $vm->setInteg($integ->getIdInteg());
        //Save the vm first
        $app['vm.manager']->save($vm);

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

    $app[ 'session' ]->set('flash', array(
        'type'    =>'success',
        'short'   =>'Your prototype will be ready soon.',
        'ext'     =>'You will receive an email as soon as it will be ready to be use.',
    ));

    return $app->redirect('/');
})
->bind('launch-prototype');


# Include or render for twig

$app->get('/_status', function () use ($app) {
    $vms = $app['vm.manager']->loadVm();
    return $app['twig']->render('_status.html', array('vms' => $vms, 'default_expiration_delay' => Vm::EXPIRED_AT_DEFAULT_VALUE));
})
->bind('_status');

$app->post('/_comment', function(Request $request) use ($app){
    $app['vm.manager']->comment($request->get('id'), $request->get('value'));
    return $request->get('value');
});

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

    if ($request->get('_route') === 'login') {
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
