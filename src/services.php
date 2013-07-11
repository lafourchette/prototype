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
    return new LaFourchette\Manager\UserManager($app['orm.em'],'\LaFourchette\Entity\user');
});

$app['project.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\ProjectManager($app['orm.em'],'\LaFourchette\Entity\Project');
});

$app['integ_availabibilty.checker'] = $app->share(function() use ($app){
    return new \LaFourchette\Checker\IntegAvailabibiltyChecker($app['integ.manager']);
});

$app['github.manager'] = $app->share(function() use ($app){
   return new \LaFourchette\Manager\GithubManager($app['project.manager']); 
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
    $notify = new \LaFourchette\Notify();
    $notify->addNotifyMessage('expired', new \LaFourchette\Notify\Expired());
    $notify->addNotifyMessage('ready', new \LaFourchette\Notify\Ready());
    $notify->addNotifyMessage('killed', new \LaFourchette\Notify\Killed());
    $notify->addNotifyMessage('unable_to_start', new \LaFourchette\Notify\UnableToStart());
    return $notify;
});

$app['vm.provisionner'] = $app->share(function() use ($app) {
    //TODO: use a factory
    $provisionner = new \LaFourchette\Provisioner\Vagrant();
    return $provisionner;
});

$app['vm.service'] = $app->share(function() use ($app) {
    $vmService = new \LaFourchette\Service\VmService();
    $vmService->setVmManager($app['vm.manager']);
    $vmService->setProvisionner($app['vm.provisionner']);
    $vmService->setNotify($app['notify.service']);

    return $vmService;
});
