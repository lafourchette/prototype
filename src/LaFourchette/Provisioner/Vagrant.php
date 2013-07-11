<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Exception\UnableToStartException;
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
                $now = new \DateTime();
                if ($now > $vm->getExpiredDt()) {
                    return VM::EXPIRED;
                }
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

        $cmd = 'git pull';
        $this->run($vm, $cmd);

        $this->generateFact($vm);

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

    public function stop(VM $vm)
    {
        $cmd = 'vagrant halt --force';
        $this->run($vm, $cmd);
    }

    public function initialise(VM $vm)
    {
        $cmd = 'git clone git@github.com:lafourchette/lafourchette-vm.git .';
        $this->run($vm, $cmd);

        $this->generateFact($vm);
    }

    protected function generateFact(Vm $vm)
    {
        $integ  = $vm->getInteg();
        $mac = str_replace(':', '', $integ->getMac());

        $branches['branches_lafourchette_portal'] = 'master';
        $branches['branches_lafourchette_mailer'] = 'master';
        $branches['branches_lafourchette_module'] = 'master';
        $branches['branches_lafourchette_rr'] = 'dev-puppetized';
        $branches['branches_lafourchette_bo'] = 'master';
        $branches['branches_lafourchette_core'] = 'master';
        $branches['branches_lafourchette_webmobile'] = 'master-fr-ch';
        $branches['branches_lafourchette_b2b'] = 'dev-puppetized';

        $vmProjects = $vm->getVmProjects();

        foreach ($vmProjects as $vmProject) {
            $project = $vmProject->getProject();
            switch ($project->getName()) {
                case 'lafourchette-portal':
                    $branches['branches_lafourchette_portal'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-mailer':
                    $branches['branches_lafourchette_mailer'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-module':
                    $branches['branches_lafourchette_module'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-rr':
                    $branches['branches_lafourchette_rr'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-bo':
                    $branches['branches_lafourchette_bo'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-core':
                    $branches['branches_lafourchette_core'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-webmobile':
                    $branches['branches_lafourchette_webmobile'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-b2b':
                    $branches['branches_lafourchette_b2b'] = $vmProject->getBranch();
                    break;
            }
        }

        $suffix = $integ->getSuffix();
        $ip = $integ->getIp();
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
    'suffix' => '{$suffix}',

    # Branches
    'branches_lafourchette_portal' => '{$branches['branches_lafourchette_portal']}',
    'branches_lafourchette_recovery' => '{$branches['branches_lafourchette_mailer']}',
    'branches_lafourchette_mailer' => '{$branches['branches_lafourchette_mailer']}',
    'branches_lafourchette_module' => '{$branches['branches_lafourchette_module']}',
    'branches_lafourchette_rr' => '{$branches['branches_lafourchette_rr']}',
    'branches_lafourchette_bo' => '{$branches['branches_lafourchette_bo']}',
    'branches_lafourchette_core' => '{$branches['branches_lafourchette_core']}',
    'branches_lafourchette_webmobile' => '{$branches['branches_lafourchette_webmobile']}',
    'branches_lafourchette_b2b' => '{$branches['branches_lafourchette_b2b']}'
  },
  # Key used for cloning lf repos. Copied at VM startup
  'github_private_key' => '{$githubKey}',
  'node' => 'vm.lafourchette.local',
  'debug' => false,
  'nfs' => false,
  'share' => false,
  'network_type' => 'public',
  'ip' => '{$ip}',
  'bridge' => 'eth0',
  'mac' => '{$mac}' # used only in public network
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

    public function delete(VM $vm)
    {
        $this->stop($vm);
    }
}