<?php

namespace LaFourchette\Manager;

use LaFourchette\Manager\Doctrine\ORM\AbstractManager;
use LaFourchette\Entity\Vm;
use Doctrine\ORM\Query\ResultSetMapping;

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
        $qb = $this->repository->createQueryBuilder('i')
                ->select('COUNT(i)');

        return $qb->getQuery()->getSingleResult();
    }

    public function getBestInteg()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('LaFourchette\Entity\Integ', 'i');
        $rsm->addFieldResult('i', 'id_integ', 'idInteg'); // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'name', 'name'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'suffix', 'suffix'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'path', 'path'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'server', 'server'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'ssh_key', 'sshKey'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'ssh_user', 'sshUser'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'ip', 'ip'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'mac', 'mac'); // // ($alias, $columnName, $fieldName)
        $rsm->addFieldResult('i', 'github_key', 'githubKey'); // // ($alias, $columnName, $fieldName)

        $query = $this->em->createNativeQuery('select integ.id_integ, integ.name, integ.suffix, integ.path, integ.server, integ.ssh_key, integ.ssh_user, integ.ip, integ.mac, integ.github_key from integ left join vm on integ.id_integ = vm.id_integ and vm.status in (:status) where vm.id_vm is null order by random() LIMIT 1', $rsm);
        $query->setParameter(':status', array(Vm::RUNNING, Vm::SUSPEND));

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            return null;
        }
    }

}