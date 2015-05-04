<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\Vm;
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

    public function getStatus(Vm $vm)
    {
        $this->checkForDestinationDirectory($vm);

        if (new \DateTime() > $vm->getExpiredDt()) {
            return Vm::EXPIRED;
        }

        // @todo It's running by default as we don't really know what should we check against to state about the Vm's status
        return Vm::RUNNING;
    }

    public function start(Vm $vm)
    {
        if (VM::RUNNING === $this->getStatus($vm)) {
            throw new \Exception('VM is already running');
        }

        $this->initialise($vm);

        // @todo Should we add specific data to the already generated data file?
    }

    public function stop(Vm $vm)
    {
        // @todo Which action should be taken based on the generated data file in order to "stop the VM"?
        throw new \BadMethodCallException('Not implemented!');
    }

    public function initialise(Vm $vm)
    {
        parent::initialise($vm);

        $this->generateDataFile($vm);
    }

    public function reset(Vm $vm)
    {
        throw new \BadMethodCallException('This is not supported. Delete the VM.');
    }

    public function delete(Vm $vm)
    {
        parent::delete($vm);
    }

    private function generateDataFile(Vm $vm)
    {
        $dataFile = <<<EOS
{
    "data": {
        "identifier_1" : "value_1",
        "identifier_2" : "value_2",
        "identifier_x" : "value_x",
    }
}
EOS;

        $this->sendfile($vm, 'dataFile', $dataFile);
    }
}
