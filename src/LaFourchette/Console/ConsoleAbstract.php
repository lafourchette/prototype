<?php

namespace LaFourchette\Console;

use LaFourchette\Manager\VmManager;
use LaFourchette\Service\NotifyService;
use Knp\Command\Command;

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
}
