<?php

namespace LaFourchette\Event;

use LaFourchette\Entity\UserNotify;

class NotifyEvent extends Event
{
    protected $userNotify;

    public function __construct(UserNotify $userNotify)
    {
        $this->userNotify = $userNotify;
    }

    public function getNotify()
    {
        return $this->userNotify;
    }
}