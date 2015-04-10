<?php
/**
 * Created by PhpStorm.
 * User: suarezd
 * Date: 10/04/2015
 * Time: 16:44
 */

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;

class Dummy extends ProvisionerAbstract {
    const OUTPUT_FILE = 'toto.log';

    public function getStatus(VM $vm)
    {
        // TODO: Implement getStatus() method.
    }

    public function start(VM $vm)
    {
//        $cmd = 'vagrant up --no-provision';
        $cmd = 'echo provisioner start > ' . OUTPUT_FILE;
        $this->run($vm, $cmd, false, false);
    }

    public function stop(VM $vm)
    {
        $this->run($vm, 'echo provisioner stop > ' . OUTPUT_FILE, false, false);
    }

    public function initialise(VM $vm)
    {
        // TODO: Implement initialise() method.
    }

    public function reset(VM $vm)
    {
        // TODO: Implement reset() method.
    }

    public function delete(VM $vm)
    {
        // TODO: Implement delete() method.
    }
}