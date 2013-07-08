<?php

namespace LaFourchette\Creator;

use LaFourchette\Decider\IntegDecider;
use LaFourchette\Entity\Vm;

/**
 * PrototypeCreator Creator
 *
 * @author gcavana
 */
class VmCreator implements CreatorInterface
{

    protected $integDecider;

    public function __construct(IntegDecider $integDecider)
    {
        $this->integDecider = $integDecider;
    }

    public function create()
    {
        $integ = $this->integDecider; // Integ Entity
        $branch = 'master';
        $project = 
        
        $vm = new Vm();
        $vm->setCreatedBy('guillaume_cavana'); //TODO add user through ldap
        $vm->setCreateDt(new \DateTime());
        $vm->setUpdateDt(new \DateTime());
        $vm->setExpiredDt(new \DateTime(strtotime(sprinf('+%d hours', Vm::EXPIRED_AT_DEFAULT_VALUE))));
        $vm->setInteg($integ);

        $vmProject = new \LaFourchette\Entity\VmProject();
        $vmProject->setVm($vm);
        $vmProject->setBranch($branch);
        $vmProject->setProject($project);
        
        
        return $vmProject;
    }

    public function getName()
    {
        return 'vm_creator';
    }

}