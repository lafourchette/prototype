<?php

namespace LaFourchette\Ldap;

use LaFourchette\Entity\User;

interface LdapManagerInterface
{
    /**
     * Connect to the LDAP
     */
    public function connect();

    /**
     * @param  string $userDn
     * @param  string $password
     * @return array
     */
    public function bind($userDn, $password);

    /**
     * @param  string $username
     * @return User
     */
    public function getUserInfo($username);

    /**
     * @return array
     */
    public function listUsers();

}
