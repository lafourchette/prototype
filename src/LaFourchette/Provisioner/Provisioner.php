<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\IntegManager;

abstract class Provisioner implements ProvisionerInterface
{
    protected $integManager;

    /**
     * @param $id
     *
     * @return \LaFourchette\Entity\Integ
     */
    public function getInteg($id)
    {
        return $this->integManager->load($id);
    }

    public function __construct(IntegManager $integManager)
    {
        $this->integManager = $integManager;
    }

    public function initialise(VM $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "mkdir -p $path", false);
        $this->cleanUp($vm);
    }

    public function delete(VM $vm)
    {
        $this->cleanUp($vm);
    }

    private function cleanUp(VM $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "rm -rf $path/*; rm -rf $path/.*", false);
    }
}
