<?php

namespace LaFourchette\Ldap;

use LaFourchette\Entity\User;

/**
 * Class MockLdapManager
 * @package LaFourchette\Ldap
 * @author Florian B
 */
class MockLdapManager extends LdapManager implements LdapManagerInterface
{

    const USER_NAME = 'Anonymous';
    const USER_EMAIL = 'anonymous@lafourchette.fr';

    function __construct()
    {
        $this->username = self::USER_NAME;
    }

    /**
     * @return mixed
     */
    public function connect()
    {
        $this->ldapRes = true;
    }

    /**
     * @param type $userDn
     * @param type $password
     * @return type
     */
    public function bind($userDn, $password)
    {
        return true;
    }

    /**
     * @param type $username
     * @return null|\LaFourchette\Entity\User
     */
    public function getUserInfo($username)
    {
        if (null === $this->ldapRes) {
            $this->connect();
        }

        $user = new User();
        $user->setUsername(self::USER_NAME);
        $user->setEmail(self::USER_EMAIL);

        return $user;
    }

    /**
     * @return mixed
     */
    public function listUsers()
    {
        if (null === $this->ldapRes) {
            $this->connect();
        }

        return array(array(
           'email' => self::USER_EMAIL,
           'username' => self::USER_NAME,
        ));
    }


}
 