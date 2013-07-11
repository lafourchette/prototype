<?php

namespace LaFourchette\Console;

use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Vagrant;
use Silex\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ConsoleAbstract
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @param Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    abstract public function run(InputInterface $input, OutputInterface $output);
    abstract static public function register(Application $app, \Symfony\Component\Console\Application $console);

    /**
     * @return VmManager
     */
    public function getVmManager()
    {
        $app = $this->getApplication();
        $vmManager = $app['vm.manager'];

        return $vmManager;
    }

    public function getNotify()
    {
        $app = $this->getApplication();
        $notifyService = $app['notify.service'];

        return $notifyService;
    }
}