<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaFourchette\Entity\Vm;
use LaFourchette\Entity\Project;

/**
 * @ORM\Entity
 * @ORM\Table(name="vm_project")
 */
class VmProject
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="LaFourchette\Entity\Vm", inversedBy="vmProjects")
     * @ORM\JoinColumn(name="id_vm", referencedColumnName="id_vm")
     * @var Vm
     */
    protected $vm;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="LaFourchette\Entity\Project", inversedBy="vmProjects")
     * @ORM\JoinColumn(name="id_project", referencedColumnName="id_project")
     * @var Project
     */
    protected $project;

    /**
     * @ORM\Column(type="string", name="branch")
     * @var int
     */
    protected $branch;
    
    public function getVm()
    {
        return $this->vm;
    }

    public function setVm(Vm $vm)
    {
        $this->vm = $vm;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

}