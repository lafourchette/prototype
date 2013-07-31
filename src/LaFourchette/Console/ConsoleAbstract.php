<?php

namespace LaFourchette\Console;

use LaFourchette\Manager\VmManager;
use Silex\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LaFourchette\Service\NotifyService;

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function run(InputInterface $input, OutputInterface $output);

    /**
     * @param Application $app
     * @param \Symfony\Component\Console\Application $console
     * @return mixed
     */
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

    /**
     * @return NotifyService
     */
    public function getNotify()
    {
        $app = $this->getApplication();
        $notifyService = $app['notify.service'];

        return $notifyService;
    }
}