<?php

namespace LaFourchette\Console;

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

    public function getVmManager()
    {
        $app = $this->getApplication();
        $vmManager = $app['vm.manager'];

        return $vmManager;
    }

    public function getProvisioner()
    {
        $vmManager = $this->getVmManager();

        $provisioner = new Vagrant();
        $provisioner->setVmManager($vmManager);

        return $provisioner;
    }
}