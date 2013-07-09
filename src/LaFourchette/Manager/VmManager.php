<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;

/**
 * Description of VmManager
 *
 * @author gcavana
 */
class VmManager extends AbstractManager
{

    protected $vmProjectManager;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $class, $vmProjectManager)
    {
        parent::__construct($em, $class);
    }
    
    public function count()
    {
        $qb = $this->repository->createQueryBuilder('v')
                ->select('COUNT(v)');

        return $qb->getQuery()->getSingleResult();
    }
}