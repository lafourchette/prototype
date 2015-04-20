<?php

$app['vm.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\Vm');
});

$app['integ.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\IntegManager($app['orm.em'], $app['config'],'\LaFourchette\Entity\Integ');
});

$app['user.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\UserManager($app['orm.em'],'\LaFourchette\Entity\User');
});

$app['user_notify.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\UserNotifyManager($app['orm.em'],'\LaFourchette\Entity\UserNotify');
});

$app['vm.creator'] = $app->share(function() use ($app){
   return new \LaFourchette\Creator\VmCreator($app['integ.decider']);
});

$app['integ.decider'] = $app->share(function() use ($app){
   return new \LaFourchette\Decider\IntegDecider($app['integ.manager']);
});

$app['notify.service'] = $app->share(function() use ($app) {
    $notify = new \LaFourchette\Service\NotifyService(/*$app['hipchat.client']*/);
    $notify->addNotifyMessage('expired', new \LaFourchette\Notify\Expired());
    $notify->addNotifyMessage('expire_soon', new \LaFourchette\Notify\ExpireSoon($app['config']['vm.to_expire_in']));
    $notify->addNotifyMessage('ready', new \LaFourchette\Notify\Ready());
    $notify->addNotifyMessage('killed', new \LaFourchette\Notify\Killed());
    $notify->addNotifyMessage('unable_to_start', new \LaFourchette\Notify\UnableToStart());
    return $notify;
});

$app['vm.provisionner'] = $app->share(function() use ($app) {
    //TODO: use a factory
    $provisionner = new \LaFourchette\Provisioner\Vagrant($app['config']['vm.repo'], $app['config']['vm.default.branch']);
    return $provisionner;
});

$app['vm.provisionner2'] = $app->share(function() use ($app) {
    //TODO: use a factory
    $provisionner = new \LaFourchette\Manager\Vagrant(
        $app['integ.manager'],
        isset($app['config']['provisioners']) ? $app['config']['provisioners'] : array()
    );
    return $provisionner;
});

$app['vm.service'] = $app->share(function() use ($app) {
    $vmService = new \LaFourchette\Service\VmService();
    $vmService->setVmManager($app['vm.manager']);
    $vmService->setProvisionner(\LaFourchette\Entity\Vm::TYPE_DEFAULT, $app['vm.provisionner']);
    $vmService->setProvisionner(\LaFourchette\Entity\Vm::TYPE_V2, $app['vm.provisionner2']);
    $vmService->setNotifyService($app['notify.service']);

    return $vmService;
});

$app['login.basic_login_response'] = $app->share(function() use ($app) {
    $response = new \Symfony\Component\HttpFoundation\Response();
    $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Ldap Authentication'));
    $response->setStatusCode(401, 'Please sign in.');

    return $response;
});

$app['ldap.manager'] = $app->share(function() use ($app){

    if ($app['debug']) { // anonymous connection in debug mode
        return new \LaFourchette\Ldap\MockLdapManager();
    }

    return new \LaFourchette\Ldap\LdapManager(
        $app['config']['ldap.host'],
        $app['config']['ldap.port'],
        $app['config']['ldap.username'],
        $app['config']['ldap.password'],
        $app['config']['ldap.basedn']
    );
});

$app['vm.cc.exporter'] = $app->share(function() use ($app){
    return new \LaFourchette\Exporter\CcExporter($app['vm.manager'], $app['url_generator']);
});

$app['hipchat.client'] = $app->share(function() use ($app){
    return new HipChat\HipChat($app['config']['hipchat']); // notification...
});
