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

    public function loadAllAvailable()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('LaFourchette\Entity\Integ', 'i');
        $rsm->addFieldResult('i', 'id_integ', 'idInteg');
        $rsm->addFieldResult('i', 'name', 'name');
        $rsm->addFieldResult('i', 'suffix', 'suffix');
        $rsm->addFieldResult('i', 'path', 'path');
        $rsm->addFieldResult('i', 'ssh_key', 'sshKey');
        $rsm->addFieldResult('i', 'ssh_user', 'sshUser');
        $rsm->addFieldResult('i', 'ip', 'ip');
        $rsm->addFieldResult('i', 'mac', 'mac');
        $rsm->addFieldResult('i', 'bridge', 'bridge');
        $rsm->addFieldResult('i', 'github_key', 'githubKey');
        $rsm->addFieldResult('i', 'netmask', 'netmask');

        $query = $this->em->createNativeQuery('select integ.id_integ, integ.name, integ.suffix, integ.path, integ.ssh_key, integ.ssh_user, integ.ip, integ.mac, integ.bridge, integ.netmask, integ.github_key from integ left join vm on integ.id_integ = vm.id_integ and vm.status not in (:status) where vm.id_vm is null and integ.is_actived = 1 order by integ.name ASC', $rsm);
        $query->setParameter(':status', Vm::$freeStatus);

        return $query->getResult();
    }
}
