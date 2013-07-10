<?php

namespace LaFourchette;

use LaFourchette\Entity\Vm;

class Notify
{

    protected $notify = array();


    public function addNotifyMessage($name, $notifyMessage)
    {
        $this->notify[$name] = $notifyMessage;
    }

    public function send($name, Vm $vm)
    {
        $message = $this->factory($name);
    }

    public function factory($name)
    {
        if (!isset($this->notify[$name])) {
            throw new \Exception(sprintf('Unknown message %s', $name));
        }

        return $this->notify[$name];
    }
}