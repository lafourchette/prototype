<?php

namespace LaFourchette\Manager;

use LaFourchette\Service\DataAccessService;

/**
 * Description of AbstractManager
 *
 * @author gcavana
 */
abstract class AbstractManager implements ManagerInterface
{
    protected $dataAccessService;

    public function __construct(DataAccessService $dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }

    /**
     * {@inheritDoc}
     */
    public function load($id)
    {
        return $this->dataAccessService->load($this, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function loadOneBy(array $criteria)
    {
        return $this->dataAccessService->loadOneBy($this, $criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function loadBy(array $criteria, array $order = null)
    {
        return $this->dataAccessService->loadBy($this, $criteria, $order);
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll()
    {
        return $this->dataAccessService->findAll($this);
    }

    /**
     * {@inheritDoc}
     */
    public function flush($entity)
    {
        $this->dataAccessService->save($entity);
    }

    public function save($entity)
    {
        $this->dataAccessService->save($entity);
    }
}
