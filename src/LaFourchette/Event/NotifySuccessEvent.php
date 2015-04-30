<?php

namespace LaFourchette\Event;

use LaFourchette\Entity\UserNotify;

class NotifySuccessEvent extends Event
{
    protected $notifySuccess;

    public function __construct(UserNotify $notifySuccess)
    {
        $this->notifySuccess = $notifySuccess;
    }

    public function getNotifySuccess()
    {
        return $this->notifySuccess;
    }
}