<?php

namespace LaFourchette\Creator;

use LaFourchette\Entity\VmProject;
use LaFourchette\Entity\Project;
use LaFourchette\Entity\Vm;

/**
 * VmCreator Creator
 *
 * @author gcavana
 */
class VmProjectCreator implements CreatorInterface
{
    protected $branch;
    protected $project;
    protected $vm;

    public function addBranch($branch)
    {
        $this->branch = $branch;
    }
    
    public function addProject(Project $project)
    {
        $this->project = $project;
    }
    
    public function addVm(Vm $vm)
    {
        $this->vm = $vm;
    }
    
    public function create()
    {
        $vmProject = new VmProject();
        $vmProject->setVm($this->vm);
        $vmProject->setProject($this->project);
        $vmProject->setBranch($this->branch);

        return $vmProject;
    }

    public function getName()
    {
        return 'vm_project_creator';
    }

}