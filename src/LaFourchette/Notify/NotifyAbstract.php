<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

abstract class NotifyAbstract
{
    /**
     * @param Vm $vm
     * @return string
     */
    abstract public function getContent(Vm $vm);

    /**
     * @param Vm $vm
     * @return string
     */
    abstract public function getSubject(Vm $vm);
}