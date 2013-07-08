<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use Symfony\Component\Process\Process;

class Vagrant extends ProvisionerAbstract
{
    protected $depot = 'git@github.com:lafourchette/lafourchette-vm.git';


    protected function getPrefixCommand($integ, $realCommand)
    {
        $cmd = '';

        $sshUser = $integ->getSshUser();
        $sshKey = $integ->getSshKey();
        $server = $integ->getServer();

        if (trim($sshUser) != '' && trim($server) != '') {
            $encapsultate = 'ssh ' . $sshUser . '@' . $server . ' ';
        }

        $path = $integ->getPath();

        if (trim($path) !== '') {
            $cmd .= 'cd ' . $path . '; ';
        } else {
            throw new \Exception('Seriously ? no path ? I can deploy the VM everywhere ?');
        }

        $cmd .= $realCommand;

        if (isset($encapsultate)) {
            $cmd = $encapsultate . ' "' . str_replace('"', '\"', $cmd) . '"';
        }

        return $cmd;
    }

    public function getStatus(VM $vm)
    {
        $path = $vm->getInteg()->getPath();
        $cmd = 'ls -a ' . $path;
        $output = $this->run($vm, $cmd);

        $result = explode("\n", $output);

        if (count($result) == 0) {
            throw new \Exception('Destination directory does not exists');
        } else if (count($result) == 3 && $result = array('.', '..', '')) {
            return VM::MISSING;
        } else {
            $output = $this->run($vm, 'vagrant status');

            if (strpos($output, 'Please update Facts file !') !== false) {
                return VM::STOPPED;
            } else if (strpos($output, ' running (') !== false) {
                return VM::RUNNING;
            } else if (strpos($output, ' not created (') !== false) {
                return VM::STOPPED;
            } else if (strpos($output, ' poweroff (') !== false) {
                return VM::STOPPED;
            } else if (strpos($output, ' saved (') !== false) {
                return VM::SUSPEND;
            } else {
                throw new \Exception('Can not determine the status of the VM');
            }
        }
    }

    protected function run(VM $vm, $cmd)
    {
        $cmd = $this->getPrefixCommand($vm->getInteg(), $cmd);
        $process = new Process($cmd);
        $process->setTimeout(0);
        $process->run();

        return $process->getOutput();
    }

    public function start(VM $vm)
    {
        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
                new \Exception('VM is already running');
            case VM::RUNNING:
                new \Exception('VM is already running');
            case VM::STOPPED:
                //Do nothing;
                break;
            case VM::MISSING:
                $this->initialise($vm);
                break;
        }

        //Do Fact
        $cmd = 'cp Facts.dist Facts';



//        $cmd = 'vagrant up';
//        $cmd = $this->getPrefixCommand($integ, $cmd);
//        $process = new Process($cmd);
//        $process->run();

    }

    public function stop(VM $vm)
    {
        $cmd = 'vagrant halt --force';
        $this->run($vm, $cmd);
    }

    public function initialise(VM $vm)
    {
        $cmd = 'git clone git@github.com:lafourchette/lafourchette-vm.git .';
        $this->run($vm, $cmd);
    }

    public function reset(VM $vm)
    {
        $this->stop($vm);
        $this->start($vm);
    }
}