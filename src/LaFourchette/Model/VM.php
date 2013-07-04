<?php

namespace LaFourchette\Model;

class VM extends ModelAbstract
{
    /**
    * @var int
    */
    protected $idInteg;

    /**
    * @var int
    */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $createDt;

    /**
     * @var \DateTime
     */
    protected $updateDt;

    /**
     * @var \DateTime
     */
    protected $deleteDt;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var User
     */
    protected $createdBy;
}
