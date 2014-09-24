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
        $rsm->addJoinedEntityResult('LaFourchette\Entity\Node', 'n', 'i', 'node');
        $rsm->addFieldResult('n', 'id_node', 'idNode');
        $rsm->addFieldResult('n', 'nodeName', 'name');
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

        $query = $this->em->createNativeQuery('select
            i.id_integ,
            i.name,
            i.suffix,
            i.path,
            i.ssh_key,
            i.ssh_user,
            i.ip,
            i.mac,
            i.bridge,
            i.netmask,
            i.github_key,
            n.name as nodeName,
            n.id_node
          from integ i
          inner join node n on i.id_node = n.id_node
          left join vm on i.id_integ = vm.id_integ
          and vm.status not in (:status) where vm.id_vm is null and i.is_actived = 1 order by i.name ASC', $rsm);
        $query->setParameter(':status', Vm::$freeStatus);

        return $query->getResult();
    }
}
