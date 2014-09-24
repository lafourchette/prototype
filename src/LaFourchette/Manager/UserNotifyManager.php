<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;

/**
 * Description of UserNotifyManager
 *
 */
class UserNotifyManager extends AbstractManager
{

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        parent::__construct($em, $class);
    }
}
