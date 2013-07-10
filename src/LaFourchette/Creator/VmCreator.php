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
        $vm->setName('VM-'.$this->generateRandomString());
        $vm->setCreatedBy($user); //TODO add user through ldap
        $vm->setStatus(Vm::TO_START);
        $vm->setCreateDt(new \DateTime());
        $vm->setUpdateDt(new \DateTime());
        $vm->setExpiredDt($expiredAt);
        $vm->setInteg($integ);

        return $vm;
    }

    //@TODO add it in a tool class
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function getName()
    {
        return 'vm_creator';
    }

}