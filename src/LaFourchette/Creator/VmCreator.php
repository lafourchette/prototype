<?php

namespace LaFourchette\Creator;

use LaFourchette\Decider\IntegDecider;
use LaFourchette\Entity\Vm;

/**
 * VmCreator Creator
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
        $integ = $this->integDecider->decide(); // Integ Entity

        $user = new \LaFourchette\Entity\User();
        $user->setIdUser(1);
        $user->setUsername('guillaume_cavana');
        $user->setEmail('gcavana@lafourchette.com');
        
        $expiredAt = new \DateTime();
        $expiredAt->add(new \DateInterval(sprintf('PT%dH', Vm::EXPIRED_AT_DEFAULT_VALUE)));

        $vm = new Vm();
        $vm->setName($name);
        $vm->setCreatedBy($user); //TODO add user through ldap
        $vm->setCreateDt(new \DateTime());
        $vm->setUpdateDt(new \DateTime());
        $vm->setExpiredDt($expiredAt);
        $vm->setInteg($integ);
        
        return $vm;
    }

    public function getName()
    {
        return 'vm_creator';
    }

}