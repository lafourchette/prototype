<?php

namespace LaFourchette\Console;

use LaFourchette\Lock\LockInterface;
use LaFourchette\Lock\PidFileLock;
use LaFourchette\Manager\VmManager;
use LaFourchette\Service\NotifyService;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ConsoleAbstract extends Command
{
    /**
     * @return VmManager
     */
    public function getVmManager()
    {
        $app = $this->getSilexApplication();
        $vmManager = $app['vm.manager'];

        return $vmManager;
    }

    /**
     * @return NotifyService
     */
    public function getNotify()
    {
        $app = $this->getSilexApplication();
        $notifyService = $app['notify.service'];

        return $notifyService;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new PidFileLock();
        $lock->setDirectory($this->getProjectDirectory());
        $lock->setName($this->getName());
        if ($lock->check() !== LockInterface::CHECK_RETURN_NOLOCK
            && $lock->check() !== LockInterface::CHECK_RETURN_DEADLOCK) {
            exit('Lock already acquired, application must be running.');
        }

        try {
            $lock->acquire();
            $this->process($input, $output);
        } catch (\Exception $e) {
            $lock->release();
            throw $e;
        }
    }

    protected function process(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('Please implement this function.');
    }
}
