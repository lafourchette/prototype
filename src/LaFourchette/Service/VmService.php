<?php

namespace LaFourchette\Service;

use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use LaFourchette\Notify;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use LaFourchette\Provisioner\ProvisionerInterface;
use LaFourchette\Logger\VmLogger;

class VmService
{

    /**
     * @var VmManager|null
     */
    protected $vmManager = null;

    /**
     * @var ProvisionerInterface[]
     */
    protected $provisionners = array();

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

    /**
     * @param $type
     * @param ProvisionerInterface $provisionner
     */
    public function setProvisionner($type, ProvisionerInterface $provisionner)
    {
        $this->provisionners[$type] = $provisionner;
    }

    /**
     * @param $type
     * @return ProvisionerInterface
     */
    public function getProvisionner(Vm $vm)
    {
        return $this->provisionners[$vm->getType()];
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
        $provisioner = $this->getProvisionner($vm);
        $provisioner->initialise($vm);
        $vm->setStatus(VM::STOPPED);
        $vmManager->flush($vm);
    }

    public function delete(Vm $vm, $force = false)
    {
        $now = new \DateTime();
        $day = $now->format('w');
        if ( ($day == 0 || $day == 6) && ! $force) {
            throw new \Exception('Cannot delete a VM on weekend unless you force it');
        }

        $vmManager = $this->getVmManager();
        $provisioner = $this->getProvisionner($vm);
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
        $provisionner = $this->getProvisionner($vm);

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
        $provisionner = $this->getProvisionner($vm);

        $vm->setStatus(VM::STARTED);
        $vmManager->flush($vm);
        try {
            $provisionner->start($vm, $provisionEnable);

            $vm->setStatus(VM::RUNNING);
            $vmManager->flush($vm);
            $notify->send('ready', $vm);

        } catch (UnableToStartException $e) {
            $vm->setStatus(VM::STOPPED);
            $vmManager->flush($vm);
            $notify->send('unable_to_start', $vm);
            throw $e;
        }
    }

    public function getStatus(Vm $vm)
    {
        return $this->getProvisionner($vm)->getStatus($vm);
    }

    public function stop(Vm $vm)
    {
        $vmManager = $this->getVmManager();

        /**
         * @var VM $vm
         */
        $provisionner = $this->getProvisionner($vm);
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
