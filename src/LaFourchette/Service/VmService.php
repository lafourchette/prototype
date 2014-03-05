<?php

namespace LaFourchette\Service;

use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use LaFourchette\Notify;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use LaFourchette\Provisioner\ProvisionerInterface;
use LaFourchette\Service\NotifyService;
use LaFourchette\Logger\VmLogger;

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
     * @var NotifyService|null
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

    /**
     * @param NotifyService $notifyService
     */
    public function setNotifyService(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    /**
     * @return NotifyService|null
     */
    public function getNotifyService()
    {
        return $this->notifyService;
    }

    public function initialise(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisionner();
        $provisioner->initialise($vm);
        $vm->setStatus(VM::STOPPED);
        $vmManager->flush($vm);
    }

    public function delete(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisionner();
        $provisioner->delete($vm);

        $vm->setStatus(Vm::STOPPED);
        $vmManager->flush($vm);
    }

    private function deleteLogFile(Vm $vm)
    {
        $filename = VmLogger::getLogFile($vm->getIdVm());
        if (file_exists($filename)) {
            @unlink("$filename");
        }
    }

    public function prepare(Vm $vm)
    {
        /**
         * @var VM $vm
         */
        $provisionner = $this->getProvisionner();

        //$provisionner->start($vm, true, 'integ.lafourchette.local');
        $provisionner->stop($vm);
    }

    public function start(Vm $vm, $provisionEnable = true)
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
            $provisionner->start($vm, $provisionEnable);

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

    public function archived(Vm $vm)
    {
        $vmManager = $this->getVmManager();
        $this->delete($vm);
        $vm->setStatus(VM::EXPIRED);
        $this->deleteLogFile($vm);
        $vmManager->flush($vm);
        $this->prepare($vm);
    }
}
