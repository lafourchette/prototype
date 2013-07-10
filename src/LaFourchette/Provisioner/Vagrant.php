<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\VmManager;
use Symfony\Component\Process\Process;

class Vagrant extends ProvisionerAbstract
{
    protected $depot = 'git@github.com:lafourchette/lafourchette-vm.git';

    /**
     * @var VmManager
     */
    protected $vmManager = null;

    /**
     * @param VmManager $vmManager
     */
    public function setVmManager($vmManager)
    {
        $this->vmManager = $vmManager;
    }

    /**
     * @return VmManager|null
     */
    public function getVmManager()
    {
        return $this->vmManager;
    }

    protected function getPrefixCommand($integ, $realCommand)
    {
        $cmd = '';

        $sshUser = $integ->getSshUser();
        $sshKey = $integ->getSshKey();
        $server = $integ->getServer();

        if (trim($sshUser) != '' && trim($server) != '') {
            $encapsultate = 'ssh -o "StrictHostKeyChecking no" ' . $sshUser . '@' . $server . ' ';
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

        $cmd = 'vagrant up';
        $this->run($vm, $cmd);

        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
            case VM::STOPPED:
            case VM::MISSING:
                throw new \Exception('The Vm have not started');
            case VM::RUNNING:
                $vm->setStatus(VM::RUNNING);
                $this->getVmManager()->flush($vm);
                break;
        }
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

        $githubKey = $vm->getInteg()->getGithubKey();

        $fact = <<<EOS
Facts = {
  'facter' => {
    'application_env' => 'dev',
    'user_email' => 'chuck@norris.com',
    # Used for commits
    'user_name' => 'Chuck Norris',
    'github_user' => 'chucknorris',
    'force_github_revision' => true,
    'rabbitmq_user' => 'lafourchette',
    'rabbitmq_password' => 'lafourchette',
    'rabbitmq_vhost' => 'lafourchette',
    'rabbitmq_host' => 'localhost',
    'rabbitmq_port' => '5673',
    'composer_update' => true,
  },
  # Key used for cloning lf repos. Copied at VM startup
  'github_private_key' => '{$githubKey}',
  'node' => 'dev.lafourchette.local',
  'debug' => false,
  'nfs' => false,
  'share' => false
}
EOS;

        $cmd = 'echo "'.str_replace('"', '\"', $fact).'" > Facts';
        $this->run($vm, $cmd);
    }

    public function reset(VM $vm)
    {
        $this->stop($vm);
        $this->start($vm);
    }
}