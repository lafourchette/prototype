<?php

namespace LaFourchette\Location;

use LaFourchette\Entity\Integ;

/**
 * Where an Integ is instanciated.
 */
class Location
{
    /**
     * @var int
     */
    protected $idNode;

    /**
     * @var null|string
     */
    protected $name = null;

    /**
     * @var null|string
     */
    protected $ip = null;

    /**
     * @var Integ
     */
    protected $integ;


    /**
     * @return int
     */
    public function getIdNode()
    {
        return $this->idNode;
    }

    /**
     * @param int $id
     */
    public function setIdNode($id)
    {
        $this->idNode = $id;
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
