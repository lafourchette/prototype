<?php

namespace LaFourchette\Listener;

use Symfony\Component\EventDispatcher\Event;
use LaFourchette\Notify\NotifyAbstract;

class NotificationListener
{
    public function __construct()
    {
    }

    public function onNotifyAction(Event $event)
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