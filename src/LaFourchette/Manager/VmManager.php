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
    
    public function loadVm()
    {
        $qb = $this->repository->createQueryBuilder('v');
        
        $qb->select('v')
           ->where($qb->expr()->notIn('v.status', ':status'))
           ->setParameter('status', Vm::$archiveStatus);
        
        return $qb->getQuery()->getResult();
    }

}