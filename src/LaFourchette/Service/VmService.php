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

    public function getNotifyService()
    {
        return $this->notifyService;
    }

    public function initialise(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisioner();
        $provisioner->initialise($vm);
        $vm->setStatus(VM::STOPPED);
        $vmManager->flush($vm);
    }

    public function delete(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisioner();
        $provisioner->delete($vm);

        $vm->setStatus(Vm::STOPPED);
        $vmManager->flush($vm);
    }

    public function start(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $notify = $this->getNotifyService();

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
            $notify->send('ready', $vm);

        } catch (UnableToStartException $e)
        {
            $vm->setStatus(VM::STOPPED);
            $vmManager->flush($vm);
            $notify->send('unable_to_start', $vm);
            throw $e;
        }
    }

    public function getStatus(Vm $vm)
    {
        return $this->getProvisionner()->getStatus($vm);
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