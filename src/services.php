<?php

$app['github.manager'] = $app->share(function() use ($app){
   return new \LaFourchette\Manager\GithubManager(); 
});

$app['vm.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\VM');
});

$app['integ.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\Integ');
});

$app['project.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\Project');
});

$app['integ_availabibilty.checker'] = $app->share(function() use ($app){
    
    $countInteg = $app['integ.manager']->count();
    $countVm = $app['vm.manager']->count();
    
    return new \LaFourchette\Checker\IntegAvailabibiltyChecker($countInteg, $countVm);
});