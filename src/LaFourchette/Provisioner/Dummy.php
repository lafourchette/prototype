<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\IntegManager;

class Dummy extends Provisioner
{
    /**
     * @param IntegManager $integManager
     */
    public function __construct(IntegManager $integManager)
    {
        parent::__construct($integManager);
    }

    public function getStatus(VM $vm)
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    public function start(VM $vm)
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    public function stop(VM $vm)
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    public function initialise(VM $vm)
    {
        parent::initialise($vm);

        throw new \BadMethodCallException('Not implemented!');
    }

    public function reset(VM $vm)
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    public function delete(VM $vm)
    {
        parent::delete($vm);
    }
}
