<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class VM
{

    const RUNNING = 0;
    const STOPPED = 1;
    const SUSPEND = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_vm")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $idVm;

    /**
     * @ORM\OneToOne(targetEntity="LaFourchette\Entity\Integ")
     * @ORM\JoinColumn(name="id_integ", referencedColumnName="id_integ")
     * @var object
     */
    protected $integ;

    /**
     * @ORM\Column(type="integer", name="status")
     * @var int
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $createDt;

    /**
     * @ORM\Column(type="datetime", name="update_dt")
     * @var \DateTime
     */
    protected $updateDt;

    /**
     * @ORM\Column(type="datetime", name="delete_dt")
     * @var \DateTime
     */
    protected $deleteDt;

    /**
     * @ORM\Column(type="string", name="name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="created_by")
     * @var string
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="datetime", name="expired_dt")
     * @var \DateTime
     */
    protected $expiredDt;
    
    public function getExpiredDt()
    {
        return $this->expiredDt;
    }

    public function setExpiredDt(\DateTime $expiredDt)
    {
        $this->expiredDt = $expiredDt;
    }

    
    public function getIdVm()
    {
        return $this->idVm;
    }

    public function setIdVm($id)
    {
        $this->idVm = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getCreateDt()
    {
        return $this->createDt;
    }

    public function setCreateDt(\DateTime $createDt)
    {
        $this->createDt = $createDt;
    }

    public function getUpdateDt()
    {
        return $this->updateDt;
    }

    public function setUpdateDt(\DateTime $updateDt)
    {
        $this->updateDt = $updateDt;
    }

    public function getDeleteDt()
    {
        return $this->deleteDt;
    }

    public function setDeleteDt(\DateTime $deleteDt)
    {
        $this->deleteDt = $deleteDt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @param Integ $integ
     */
    public function setInteg(Integ $integ)
    {
        $this->integ = $integ;
    }

    /**
     * @return Integ
     */
    public function getInteg()
    {
        return $this->integ;
    }

}
