<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

abstract class NotifyAbstract
{
    abstract public function getContent(Vm $vm);
}