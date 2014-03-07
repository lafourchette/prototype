<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use LaFourchette\Entity\VmProject;
use LaFourchette\Entity\User;

/**
 * @ORM\Entity
 */
class Vm
{
    const EXPIRED_AT_DEFAULT_VALUE = 24; //define in hours

    //Status
    const TO_START = -1; //If the vm need to be started
    const RUNNING = 0; //If vagrant is running
    const STOPPED = 1; //If vagrant is stopped
    const SUSPEND = 2; //If vagrant is suspend
    const MISSING = 3; //If directory is present and empty
    const EXPIRED = 4; //If a vm is expired
    const STARTED = 5; //If a vm is started
    const ARCHIVED = 6; //If a vm is expired


    public static $availableStatus = array(self::RUNNING, self::SUSPEND, self::TO_START, self::STARTED);
    public static $archiveStatus = array(self::EXPIRED, self::ARCHIVED);

    public static $freeStatus = array(self::ARCHIVED);

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
     * @ORM\OneToOne(targetEntity="LaFourchette\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id_user")
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="datetime", name="expired_dt")
     * @var \DateTime
     */
    protected $expiredDt;

    /**
     * @ORM\OneToMany(targetEntity="LaFourchette\Entity\VmProject", mappedBy="vm", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_vm", referencedColumnName="id_vm")
     * @var VmProject[]
     */
    protected $vmProjects;

    /**
     * @ORM\OneToMany(targetEntity="LaFourchette\Entity\UserNotify", mappedBy="vm", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_vm", referencedColumnName="id_vm")
     * @var UserNotify[]
     */
    protected $usersNotify;

    /**
     * @return \DateTime
     */
    public function getExpiredDt()
    {
        return $this->expiredDt;
    }

    /**
     * @param \DateTime $expiredDt
     */
    public function setExpiredDt(\DateTime $expiredDt)
    {
        $this->expiredDt = $expiredDt;
    }

    /**
     * @return int
     */
    public function getIdVm()
    {
        return $this->idVm;
    }

    /**
     * @param int $id
     */
    public function setIdVm($id)
    {
        $this->idVm = $id;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDt()
    {
        return $this->createDt;
    }

    /**
     * @param \DateTime $createDt
     */
    public function setCreateDt(\DateTime $createDt)
    {
        $this->createDt = $createDt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDt()
    {
        return $this->updateDt;
    }

    /**
     * @param \DateTime $updateDt
     */
    public function setUpdateDt(\DateTime $updateDt)
    {
        $this->updateDt = $updateDt;
    }

    /**
     * @return \DateTime
     */
    public function getDeleteDt()
    {
        return $this->deleteDt;
    }

    /**
     * @param \DateTime $deleteDt
     */
    public function setDeleteDt(\DateTime $deleteDt)
    {
        $this->deleteDt = $deleteDt;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
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

    /**
     * @return VmProject[]
     */
    public function getVmProjects()
    {
        return $this->vmProjects;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name . '-' . $this->getInteg()->getName();
    }

    /**
     * @return UserNotify[]
     */
    public function getUsersNotify()
    {
        return $this->usersNotify;
    }

    /**
     * @param UserNotify[] $usersNotify
     */
    public function setUsersNotify(array $usersNotify)
    {
        $this->usersNotify = $usersNotify;
    }

    public function getCcActivity()
    {
        if (in_array($this->getStatus(), array(self::TO_START, self::STARTED))) {
            return 'Building';
        }
        return 'Sleeping';
    }

    public function getCcStatus()
    {
        if(in_array($this->getStatus(), self::$availableStatus)) {
            return 'Success';
        } elseif(self::STOPPED == $this->getStatus()) {
            return 'Failure';
        }
    }
}
