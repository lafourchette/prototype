<?php
namespace LaFourchette\Provisioner;

interface ProvisionerInterface
{
    public function getStatus($vm);
    public function start($vm);
    public function stop($vm);

    public function initialise($vm);

    public function reset($vm);
}
