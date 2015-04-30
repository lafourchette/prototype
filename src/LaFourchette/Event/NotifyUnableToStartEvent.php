<?php

namespace LaFourchette\Event;

use LaFourchette\Entity\UserNotify;

class NotifyUnableToStartEvent extends Event
{
    protected $notifyUnableToStart;

    public function __construct(UserNotify $notifyUnableToStart)
    {
        $this->notifyUnableToStart = $notifyUnableToStart;
    }

    public function getNotifyUnableToStart()
    {
        return $this->notifyUnableToStart;
    }
}