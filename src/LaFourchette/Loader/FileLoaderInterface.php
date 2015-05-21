<?php

namespace LaFourchette\Loader;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\User;
use LaFourchette\Entity\UserNotify;
use LaFourchette\Entity\Vm;

interface FileLoaderInterface
{
    /**
     * Loads a file
     *
     * @return mixed
     */
    public function load();

    /**
     * @return Integ[]
     */
    public function getIntegs();

    /**
     * @return User[]
     */
    public function getUsers();

    /**
     * @return Vm[]
     */
    public function getVms();
}