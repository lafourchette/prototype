<?php

namespace LaFourchette\Ldap;

/**
 * Class LdapManagerInterface
 * @package LaFourchette\Ldap
 * @author Florian B
 */
interface LdapManagerInterface
{

    /**
     * @return mixed
     */
    public function connect();

    /**
     * @param type $userDn
     * @param type $password
     * @return type
     */
    public function bind($userDn, $password);

    /**
     * @param type $username
     * @return null|\LaFourchette\Entity\User
     */
    public function getUserInfo($username);

    /**
     * @return mixed
     */
    public function listUsers();

}
 