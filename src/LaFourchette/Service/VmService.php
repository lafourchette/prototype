<?php

namespace LaFourchette\Service;

use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use LaFourchette\Provisioner\ProvisionerInterface;

class VmService
{

    /**
     * @var VmManager|null
     */
    protected $vmManager = null;

    /**
     * @var ProvisionerInterface null
     */
    protected $provisionner = null;

    public function setVmManager(VmManager $vmManager)
    {
        $this->vmManager = $vmManager;
    }

    public function getVmManager()
    {
        return $this->vmManager;
    }

    public function setProvisionner(ProvisionerInterface $provisionner)
    {
        $this->provisionner = $provisionner;
    }

    public function getProvisionner()
    {
        return $this->provisionner;
    }

    public function start(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        /**
         * @var VM $vm
         */
        $provisioner = $this->getProvisioner();

        $vm->setStatus(VM::STARTED);
        $vmManager->flush($vm);
        try {
            $provisioner->start($vm);
            $vm->setStatus(VM::RUNNING);
            $vmManager->flush($vm);
        } catch (UnableToStartException $e)
        {
            $vm->setStatus(VM::STOPPED);
            $vmManager->flush($vm);
            throw $e;
        }
    }
}