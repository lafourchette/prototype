<?php

namespace LaFourchette\Ldap;

use LaFourchette\Entity\User;

class MockLdapManager extends LdapManager implements LdapManagerInterface
{

    const USER_NAME  = 'Anonymous';
    const USER_EMAIL = 'anonymous@lafourchette.fr';

    protected $email;

    public function __construct($user = '', $email = '')
    {
        $this->username = $user ? $user : self::USER_NAME;
        $this->email = $email ? $email : self::USER_EMAIL;    
    }

    public function getEmail() 
    {
        return $this->email;
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
        $user->setUsername($this->username);
        $user->setEmail($this->email);

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
           'username' => $this->username,
           'email' => $this->email
        ));
    }
}
