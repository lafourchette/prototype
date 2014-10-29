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
           ->orderBy('v.integ', 'ASC')
           ->setParameter('status', Vm::$freeStatus);

        return $qb->getQuery()->getResult();
    }

    public function getActive()
    {
        return $this->getByStatus(Vm::RUNNING);
    }

    public function getToStart()
    {
        return $this->getByStatus(Vm::TO_START);
    }

    public function comment($id, $comment)
    {
        $vm = $this->load($id);
        $vm->setComment($comment);

        $this->save($vm);
    }

    private function getByStatus($status)
    {
        $qb = $this->repository->createQueryBuilder('v');

        $qb->select('count(v)')
           ->where('v.status = :status')
           ->setParameter('status', $status);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
