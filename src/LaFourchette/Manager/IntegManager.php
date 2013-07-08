<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;

/**
 * Description of VmManager
 *
 * @author gcavana
 */
class IntegManager extends AbstractManager
{

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
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
