<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaFourchette\Entity\Node;

/**
 * @ORM\Entity
 */
class Integ
{
    /**
     * @var int
     */
    protected $idInteg;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var object
     */
    protected $node;

    /**
     * @var string
     */
    protected $sshKey;

    /**
     * @var string
     */
    protected $sshUser;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $mac;

    /**
     * @var string
     */
    protected $bridge;

    /**
     * @var string
     */
    protected $netmask;

    /**
     * @var string
     */
    protected $githubKey;

    /**
     * @var bool
     */
    protected $isActived;

    /**
     * @var object
     */
    protected $vm;

    public function getIsActived()
    {
        return $this->isActived;
    }

    public function setIsActived($isActived)
    {
        return $this->isActived = $isActived;
    }

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
        return $this;
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
        return $this;
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
        return $this;
    }

    /**
     * @return string Absolute path where the guest VM actually lives on the host.
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
        return $this;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode(Node $node)
    {
        $this->node = $node;
        return $this;
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
        return $this;
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
        return $this;
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
        return $this;
    }

    /**
     * @return string
     */
    public function getBridge()
    {
        return $this->bridge;
    }

    /**
     * @param string $bridge
     */
    public function setBridge($bridge)
    {
        $this->bridge = $bridge;
        return $this;
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
        return $this;
    }

    /**
     * @return string
     */
    public function getNetmask()
    {
        return $this->netmask;
    }

    /**
     * @param string $mac
     */
    public function setNetmask($netmask)
    {
        $this->netmask = $netmask;
        return $this;
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
        return $this;
    }

    static public function makeFromArray(array $array)
    {
        $node = new Node();
        $node->setIdNode($array['node']['id_node']);
        $node->setIp($array['node']['ip']);
        $node->setName($array['node']['name']);

        $o = new self();
        $o->setIdInteg($array['id_integ'])
            ->setName($array['name'])
            ->setSuffix($array['suffix'])
            ->setPath($array['path'])
            ->setNode($node)

            ->setSshKey($array['ssh_key'])
            ->setSshUser($array['ssh_user'])

            ->setIp($array['ip'])
            ->setMac($array['mac'])
            ->setBridge($array['bridge'])
            ->setNetmask($array['netmask'])

            ->setGithubKey($array['github_key'])
            ->setIsActived($array['is_actived'])
        ;

        return $o;
    }
}
