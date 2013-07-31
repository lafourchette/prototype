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
     * @ORM\OneToMany(targetEntity="LaFourchette\Entity\VmProject", mappedBy="project", cascade={"persist"})
     * @ORM\JoinColumn(name="id_project", referencedColumnName="id_project")
     * @var VmProject
     */
    protected $vmProjects;

    /**
     * @return int
     */
    public function getIdProject()
    {
        return $this->idProject;
    }

    /**
     * @param int $idProject
     */
    public function setIdProject($idProject)
    {
        $this->idProject = $idProject;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}