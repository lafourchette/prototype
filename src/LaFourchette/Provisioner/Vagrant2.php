<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use Symfony\Component\Process\Process;
use LaFourchette\Logger\LoggableProcess;
use LaFourchette\Logger\VmLogger;

class Vagrant2 extends Vagrant
{

    public function __construct($repo, $defaultBranch = '2.0')
    {
        $this->repo = $repo;
        $this->defaultBranch = $defaultBranch;
    }

    /**
     * @param VM $vm
     * @return mixed
     * @throws \Exception
     */
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
                $now = new \DateTime();
                if ($now > $vm->getExpiredDt()) {
                    return VM::EXPIRED;
                }
                return VM::RUNNING;
            } else if (strpos($output, ' not created (') !== false) {
                return VM::STOPPED;
            } else if (strpos($output, ' poweroff (') !== false) {
                $now = new \DateTime();
                if ($now > $vm->getExpiredDt()) {
                    return VM::EXPIRED;
                }
                return VM::STOPPED;
            } else if (strpos($output, ' saved (') !== false) {
                return VM::SUSPEND;
            } else {
            }
        }
    }

    /**
     * @param VM $vm
     * @throws Exception\UnableToStartException
     */
    public function start(VM $vm, $provisionEnable = true, $node = 'integ.lafourchette.local')
    {
        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
                throw new \Exception('VM is already running');
            case VM::RUNNING:
                throw new \Exception('VM is already running');
            case VM::STOPPED:
                //Do nothing;
                break;
            case VM::MISSING:
                $this->initialise($vm);
                break;
        }

        $cmd = sprintf(self::GIT_PULL_VM_CMD, $this->defaultBranch);
        $this->run($vm, $cmd);

        // $this->generateFact($vm, $node);

        $cmd = 'vagrant up';
        $this->run($vm, $cmd);

        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
            case VM::STOPPED:
            case VM::MISSING:
                throw new UnableToStartException('The Vm have not started');
            case VM::RUNNING:
                //TODO: nothing
                break;
        }
    }

    /**
     * @codeCoverageIgnore
     * @param  VM     $vm
     * @return void
     */
    public function initialise(VM $vm)
    {
        $cmd = sprintf(self::CLONE_VM_CMD, $this->repo, $this->defaultBranch);
        $this->run($vm, $cmd);
    }

    // @todo maybe something to fix
    protected function generateFact(Vm $vm, $node = 'integ.lafourchette.local')
    {
        $integ  = $vm->getInteg();

        // no nfs
        // share is set as false
        // network type : bridge public
        $suffix    = $integ->getSuffix(); // 'integ4' in www.lafourchette.integ4
        $ip        = $integ->getIp();     // ip of the integ
        $bridge    = $integ->getBridge(); // bridging
        $githubKey = $vm->getInteg()->getGithubKey(); // Not used anymore
        $mac = str_replace(':', '', $integ->getMac());
        $netmask = $integ->getNetmask();
    }
}
