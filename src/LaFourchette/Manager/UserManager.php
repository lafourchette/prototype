<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;
use \LaFourchette\Entity\User;

/**
 * Description of VmManager
 *
 * @author gcavana
 */
class UserManager extends AbstractManager
{

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param $ldapUser
     * @return User
     */
    public function getOrCreate($ldapUser)
    {
        $user = $this->loadOneBy(array('username' => $ldapUser->getUsername()));

        if (null === $user) {
            $this->save($ldapUser);
            $user = $this->loadOneBy(array('username' => $ldapUser->getUsername()));
        }

        return $user;
    }
}
