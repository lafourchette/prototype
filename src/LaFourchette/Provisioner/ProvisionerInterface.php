<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Model\VM;

interface ProvisionerInterface
{
    public function getStatus(VM $vm);
    public function start(VM $vm);
    public function stop(VM $vm);

    public function initialise(VM $vm);

    public function reset(VM $vm);
}
