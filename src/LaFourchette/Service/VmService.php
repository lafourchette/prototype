<?php

namespace LaFourchette\Service;

use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use LaFourchette\Notify;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use LaFourchette\Provisioner\ProvisionerInterface;

class VmService
{

    /**
     * @var VmManager|null
     */
    protected $vmManager = null;

    /**
     * @var ProvisionerInterface|null
     */
    protected $provisionner = null;

    /**
     * @var Notify|null
     */
    protected $notifyService = null;

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

    public function setNotifyService(Notify $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    public function initialise(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisioner();
        $provisioner->initialise($vm);
        $vm->setStatus(VM::STOPPED);
        $vmManager->flush($vm);
    }



    public function start(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        /**
         * @var VM $vm
         */
        $provisionner = $this->getProvisionner();

        $vm->setStatus(VM::STARTED);
        $vmManager->flush($vm);
        try {
            $provisionner->start($vm);
            $vm->setStatus(VM::RUNNING);
            $vmManager->flush($vm);
        } catch (UnableToStartException $e)
        {
            $vm->setStatus(VM::STOPPED);
            $vmManager->flush($vm);
            throw $e;
        }
    }

    public function stop(Vm $vm)
    {
        $vmManager = $this->getVmManager();

        /**
         * @var VM $vm
         */
        $provisionner = $this->getProvisionner();
        $vm->setStatus(Vm::STOPPED);
        $vmManager->flush($vm);
        $provisionner->stop($vm);
    }
}