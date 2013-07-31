<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Integ
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_integ")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $idInteg;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name = null;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $suffix;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $path;

    /**
     * @ORM\Column(type="string")
     * @var null|string
     */
    protected $server;

    /**
     * @ORM\Column(type="string", name="ssh_key")
     * @var string
     */
    protected $sshKey;

    /**
     * @ORM\Column(type="string", name="ssh_user")
     * @var string
     */
    protected $sshUser;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $ip;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $mac;

    /**
     * @ORM\Column(type="string", name="github_key")
     * @var string
     */
    protected $githubKey;

    /**
     * @ORM\ManyToOne(targetEntity="LaFourchette\Entity\Vm")
     * @ORM\JoinColumn(name="id_integ", referencedColumnName="id_vm")
     * @var object
     */
    protected $vm;


    /**
     * @return int
     */
    public function getIdInteg()
    {
        return $this->idInteg;
    }

    /**
     * @param int $id
     */
    public function setIdInteg($id)
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return null|string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getSshKey()
    {
        return $this->sshKey;
    }

    /**
     * @param string $sshKey
     */
    public function setSshKey($sshKey)
    {
        $this->sshKey = $sshKey;
    }

    /**
     * @return string
     */
    public function getSshUser()
    {
        return $this->sshUser;
    }

    /**
     * @param string $sshUser
     */
    public function setSshUser($sshUser)
    {
        $this->sshUser = $sshUser;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @param string $mac
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
    }

    /**
     * @return string
     */
    public function getGithubKey()
    {
        return $this->githubKey;
    }

    /**
     * @param string $githubKey
     */
    public function setGithubKey($githubKey)
    {
        $this->githubKey = $githubKey;
    }

}
