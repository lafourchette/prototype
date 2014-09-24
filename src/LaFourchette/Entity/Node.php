<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Node
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_node")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $idNode;

    /**
     * @ORM\Column(type="string")
     * @var null|string
     */
    protected $name = null;

    /**
     * @ORM\Column(type="string")
     * @var null|string
     */
    protected $ip = null;


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
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
