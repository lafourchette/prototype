<?php

namespace LaFourchette\Manager;

use \LaFourchette\Entity\User;

/**
 * Description of VmManager
 *
 * @author mdacosta
 */
class UserManager extends AbstractManager
{
    /**
     * @param User $ldapUser
     * @return User
     */
    public function getOrCreate(User $ldapUser)
    {
        $user = $this->dataAccessService->loadOneBy($this, array('username' => $ldapUser->getUsername()));

        if (null === $user) {
            $ldapUser->setIdUser($this->generateUserId());
            $this->dataAccessService->save($ldapUser);

            $user = $this->dataAccessService->loadOneBy($this, array('username' => $ldapUser->getUsername()));
        }

        return $user;
    }

    private function generateUserId()
    {
        return $this->dataAccessService->getNextId('user');
    }
}
