<?php

namespace LaFourchette\Manager;
use LaFourchette\Entity\Integ;

/**
 * Description of VmManager
 *
 * @author gcavana
 */
class IntegManager implements ManagerInterface
{
    private $collection = array();

    public function __construct($configuration, $class)
    {
        foreach ($configuration['integs'] as $i) {
            array_push($this->collection, $class::makeFromArray($i));
        }
    }

    public function loadAllAvailable()
    {
        return $this->collection;
    }

    /**
     * {@inheritDoc}
     */
    public function load($id)
    {
        $res = null;
        /** @var Integ $integ */
        foreach($this->collection as $integ){
            if($integ->getIdInteg() == $id){
                $res = $integ;
                break;
            }
        }
        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function loadOneBy(array $criteria)
    {
        throw new \Exception();
    }

    /**
     * {@inheritDoc}
     */
    public function loadBy(array $criteria, array $order = null)
    {
        throw new \Exception();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll()
    {
        throw new \Exception();
    }

    /**
     * {@inheritDoc}
     */
    public function flush($entity)
    {
        throw new \Exception();
    }

    public function save($entity)
    {
        throw new \Exception();
    }
}
