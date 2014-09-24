<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaFourchette\Entity\Vm;
use LaFourchette\Entity\Project;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_notify")
 */
class UserNotify
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="LaFourchette\Entity\Vm", inversedBy="usersNotify")
     * @ORM\JoinColumn(name="id_vm", referencedColumnName="id_vm")
     * @var Vm
     */
    protected $vm;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="LaFourchette\Entity\User", inversedBy="usersNotify")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * @var Project
     */
    protected $user;

    /**
     * @return Vm
     */
    public function getVm()
    {
        return $this->vm;
    }

    /**
     * @param Vm $vm
     */
    public function setVm(Vm $vm)
    {
        $this->vm = $vm;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
