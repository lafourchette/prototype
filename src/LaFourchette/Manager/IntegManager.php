<?php

namespace LaFourchette\Manager;

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

    }

    /**
     * {@inheritDoc}
     */
    public function load($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function loadOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function loadBy(array $criteria, array $order = null)
    {
        return $this->repository->findBy($criteria, $order);
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function flush($entity)
    {
        $this->em->flush($entity);
    }

    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

}
