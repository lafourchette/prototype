<?php

namespace LaFourchette\Console;

use Knp\Command\Command;

abstract class ConsoleAbstract extends Command
{
    /**
     * @return \LaFourchette\Manager\VmManager
     */
    public function getVmManager()
    {
        return $this->getSilexApplication()['vm.manager'];
    }

    /**
     * @return \LaFourchette\Service\NotifyService
     */
    public function getNotify()
    {
        return $this->getSilexApplication()['notify.service'];
    }
}
