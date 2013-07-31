<?php

namespace LaFourchette\Service;

use LaFourchette\Entity\Vm;
use LaFourchette\Notify\NotifyAbstract;

class NotifyService
{

    protected $notify = array();


    public function addNotifyMessage($name, $notifyMessage)
    {
        $this->notify[$name] = $notifyMessage;
    }

    public function send($name, Vm $vm)
    {
        $message = $this->factory($name);

        $content = $message->getContent($vm);
        $subject = $message->getSubject($vm);

        foreach ($vm->getUsersNotify() as $userNotify) {
            mail($userNotify->getUser()->getEmail(), $subject, $content);
        }
    }

    /**
     * @param $name name of template
     * @return NotifyAbstract
     * @throws \Exception
     */
    public function factory($name)
    {
        if (!isset($this->notify[$name])) {
            throw new \Exception(sprintf('Unknown message %s', $name));
        }

        return $this->notify[$name];
    }
}