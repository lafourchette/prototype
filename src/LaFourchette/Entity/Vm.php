<?php

namespace LaFourchette\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use LaFourchette\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="Vm")
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

    const TYPE_DEFAULT = 0;
    const TYPE_V2      = 1;

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
     * @ORM\Column(type="integer", name="id_integ")
     * @var int
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
     * @ORM\Column(type="string", name="comment")
     * @var string
     */
    protected $comment;

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
     * @ORM\OneToMany(targetEntity="LaFourchette\Entity\UserNotify", mappedBy="vm", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_vm", referencedColumnName="id_vm")
     * @var UserNotify[]
     */
    protected $usersNotify;

    /**
     * @ORM\Column(type="integer", name="type")
     * @var int
     */
    protected $type;

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
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
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
     * @param int $integ
     */
    public function setInteg($integ)
    {
        $this->integ = $integ;
    }

    /**
     * @return int
     */
    public function getInteg()
    {
        return $this->integ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name . '-' . $this->getInteg();
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
        if (in_array($this->getStatus(), self::$availableStatus)) {
            return 'Success';
        } elseif (self::STOPPED == $this->getStatus()) {
            return 'Failure';
        }
    }

    /**
     *
     */
    public function endsIn()
    {
        return $this->formatDateDiff($this->expiredDt);
    }

    /**
     * A sweet interval formatting, will use the two biggest interval parts.
     * On small intervals, you get minutes and seconds.
     * On big intervals, you get months and days.
     * Only the two biggest parts are used.
     *
     * @param DateTime $start
     * @param DateTime|null $end
     * @return string
     * @see http://php.net/manual/fr/dateinterval.format.php
     */
    private function formatDateDiff($start, $end=null) {
        if(!($start instanceof \DateTime)) {
            $start = new \DateTime($start);
        }

        if($end === null) {
            $end = new \DateTime();
        }

        if(!($end instanceof \DateTime)) {
            $end = new \DateTime($start);
        }

        $interval = $end->diff($start);
        $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals

        $format = array();
        if($interval->y !== 0) {
            $format[] = "%y ".$doPlural($interval->y, "year");
        }
        if($interval->m !== 0) {
            $format[] = "%m ".$doPlural($interval->m, "month");
        }
        if($interval->d !== 0) {
            $format[] = "%d ".$doPlural($interval->d, "day");
        }
        if($interval->h !== 0) {
            $format[] = "%h ".$doPlural($interval->h, "hour");
        }
        if($interval->i !== 0) {
            $format[] = "%i ".$doPlural($interval->i, "minute");
        }
        if($interval->s !== 0) {
            if(!count($format)) {
                return "less than a minute ago";
            } else {
                $format[] = "%s ".$doPlural($interval->s, "second");
            }
        }

        // We use the two biggest parts
        if(count($format) > 1) {
            $format = array_shift($format)." and ".array_shift($format);
        } else {
            $format = array_pop($format);
        }

        // Prepend 'since ' or whatever you like
        return $interval->format($format);
    }
}
