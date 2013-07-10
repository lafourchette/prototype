<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;
use LaFourchette\Entity\Vm;

/**
 * Description of VmManager
 *
 * @author gcavana
 */
class VmManager extends AbstractManager
{

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        parent::__construct($em, $class);
    }

    public function count()
    {
        $qb = $this->repository->createQueryBuilder('v');
        $qb->select('COUNT(v)')
           ->where($qb->expr()->in('v.status', ':status'))
           ->setParameter('status', array(Vm::RUNNING, Vm::SUSPEND));
        
        var_dump($qb->getQuery()->getSingleResult());
        die();
           
        return $qb->getQuery()->getSingleResult();
    }

}