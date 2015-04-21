<?php

namespace LaFourchette\Ldap;

use LaFourchette\Entity\User;

class MockLdapManager extends LdapManager implements LdapManagerInterface
{

    const USER_NAME  = 'Anonymous';
    const USER_EMAIL = 'anonymous@lafourchette.fr';

    public function __construct()
    {
        $this->username = self::USER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $this->ldapRes = true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($userDn, $password)
    {
        return true;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
