<?php

namespace LaFourchette\Ldap;

use LaFourchette\Entity\User;

/**
 * Description of LdapManager
 *
 * @author gcavana
 */
class LdapManager
{

    protected $ldapRes;
    protected $host;
    protected $username;
    protected $password;
    protected $baseDn;
    protected $port = 389;

    public function __construct($host, $port, $username, $password, $baseDn)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->baseDn = $baseDn;
    }

    public function connect()
    {
        $ress = @ldap_connect($this->host, $this->port);
        ldap_set_option($ress, LDAP_OPT_PROTOCOL_VERSION, 3);
        @ldap_bind($ress, $this->username, $this->password);

        $this->ldapRes = $ress;
    }

    /**
     * 
     * @param type $userDn
     * @param type $password
     * @return type
     */
    public function bind($userDn, $password)
    {
        if (null === $this->ldapRes) {
            $this->connect();
        }

        return @ldap_bind($this->ldapRes, $userDn, $password);
    }

    /**
     * 
     * @param type $username
     * @return null|\LaFourchette\Entity\User
     */
    public function getUserInfo($username)
    {
        if (null === $this->ldapRes) {
            $this->connect();
        }
        
        $result = @ldap_search($this->ldapRes, $this->baseDn, sprintf('uid=%s', $username));
        $entries = @ldap_get_entries($this->ldapRes, $result);
        
        if ($entries['count'] == 0) {
            return null;
        }
        
        $user = new User();
        $user->setDn($entries[0]['dn']);
        $user->setEmail($entries[0]['mail'][0]);
        $user->setUsername($username);
        
        return $user;
    }

    public function listUsers()
    {
        if (null === $this->ldapRes) {
            $this->connect();
        }

        $result = ldap_search($this->ldapRes, $this->baseDn, "(mail=*)", array("mail", "uid", "dn"));
        $entries = ldap_get_entries($this->ldapRes, $result);

        $users = array();

        for ($i = 0 ; $i < $entries['count'] ; $i++) {
            $users[] = array(
                'email' => $entries[$i]['mail'][0],
                'username' => $entries[$i]['uid'][0]
            );
        }

        return $users;
    }

}