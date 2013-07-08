<?php

$app['github.manager'] = $app->share(function() use ($app){
   return new \LaFourchette\Manager\GithubManager(); 
});

$app['vm.manager'] = $app->share(function() use ($app){
    return new \LaFourchette\Manager\VmManager($app['orm.em'],'\LaFourchette\Entity\VM');
});