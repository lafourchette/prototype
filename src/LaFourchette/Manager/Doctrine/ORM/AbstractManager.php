<?php

namespace LaFourchette\Manager\Doctrine\ORM;

use LaFourchette\Manager\ManagerInterface;

/**
 * Description of AbstractManager
 *
 * @author gcavana
 */
abstract class AbstractManager implements ManagerInterface
{
    protected $em;
    protected $repository;

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
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
