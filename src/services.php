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
    
    $countInteg = $app['integ.manager']->count();
    $countVm = $app['vm.manager']->count();
    
    return new \LaFourchette\Checker\IntegAvailabibiltyChecker($countInteg, $countVm);
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