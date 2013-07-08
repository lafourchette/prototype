<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaFourchette\Entity\VmProject;


/**
 * @ORM\Entity
 */
class Project
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_project")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $idProject;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name = null;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="LaFourchette\Entity\VmProject", mappedBy="project)
     * @ORM\JoinColumn(name="id_project", referencedColumnName="id_project"")
     * @var VmProject
     */
    protected $vmProject;
    
    
    public function getIdProject()
    {
        return $this->idProject;
    }

    public function setIdProject($idProject)
    {
        $this->idProject = $idProject;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getVmProject()
    {
        return $this->vmProject;
    }

    public function setVmProject(VmProject $vmProject)
    {
        $this->vmProject = $vmProject;
    }

}