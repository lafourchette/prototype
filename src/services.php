<?php

$app['vm.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\VM',$app['vm_project.manager']);
});

$app['integ.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\IntegManager($app['orm.em'],'\LaFourchette\Entity\Integ');
});

$app['vm_project.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\VmProjectManager($app['orm.em'],'\LaFourchette\Entity\VmProject');
});

$app['user.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\UserManager($app['orm.em'],'\LaFourchette\Entity\User');
});

$app['user_notify.manager'] = $app->share(function() use ($app){
    return new LaFourchette\Manager\UserNotifyManager($app['orm.em'],'\LaFourchette\Entity\UserNotify');
});

$app['project.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\ProjectManager($app['orm.em'],'\LaFourchette\Entity\Project');
});

$app['integ_availabibilty.checker'] = $app->share(function() use ($app){
    return new \LaFourchette\Checker\IntegAvailabibiltyChecker($app['integ.manager']);
});

$app['github.manager'] = $app->share(function() use ($app){
   return new \LaFourchette\Manager\GithubManager($app['project.manager'], $app['github.token']);
});

$app['vm.creator'] = $app->share(function() use ($app){
   return new \LaFourchette\Creator\VmCreator($app['integ.decider'], $app['project.manager']);
});

$app['integ.decider'] = $app->share(function() use ($app){
   return new \LaFourchette\Decider\IntegDecider($app['integ.manager']);
});

$app['vm_project.creator'] = $app->share(function() use ($app){
    return new \LaFourchette\Creator\VmProjectCreator();
});


$app['notify.service'] = $app->share(function() use ($app) {
    $notify = new \LaFourchette\Service\NotifyService();
    $notify->addNotifyMessage('expired', new \LaFourchette\Notify\Expired());
    $notify->addNotifyMessage('ready', new \LaFourchette\Notify\Ready());
    $notify->addNotifyMessage('killed', new \LaFourchette\Notify\Killed());
    $notify->addNotifyMessage('unable_to_start', new \LaFourchette\Notify\UnableToStart());
    return $notify;
});

$app['vm.provisionner'] = $app->share(function() use ($app) {
    //TODO: use a factory
    $provisionner = new \LaFourchette\Provisioner\Vagrant($app['vm.repo'], $app['vm.default.branch']);
    return $provisionner;
});

$app['vm.service'] = $app->share(function() use ($app) {
    $vmService = new \LaFourchette\Service\VmService();
    $vmService->setVmManager($app['vm.manager']);
    $vmService->setProvisionner($app['vm.provisionner']);
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
    return new \LaFourchette\Ldap\LdapManager($app['ldap.host'], $app['ldap.port'], $app['ldap.username'], $app['ldap.password'], $app['ldap.basedn']);
});

$app['vm.cc.exporter'] = $app->share(function() use ($app){
    return new \LaFourchette\Exporter\CcExporter($app['vm.manager'], $app['url_generator']);
});
