<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use Symfony\Component\Process\Process;
use LaFourchette\Logger\LoggableProcess;
use LaFourchette\Logger\VmLogger;

class Vagrant extends ProvisionerAbstract
{

    const CLONE_VM_CMD = 'git clone %s . && git checkout -t origin/%s';
    const GIT_PULL_VM_CMD = 'git fetch && git checkout %s && git pull';

    /**
     * @var string
     */
    protected $repo = '';


    protected $defaultBranch = '';


    public function __construct($repo, $defaultBranch)
    {
        $this->repo = $repo;
        $this->defaultBranch = $defaultBranch;
    }

    /**
     * @param $integ
     * @param string $realCommand
     * @return string
     * @throws \Exception
     */
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
     * @param string $cmd
     * @return string
     */
    protected function run(VM $vm, $cmd)
    {
        // @codeCoverageIgnoreStart
        $logger = new VmLogger();
        $logger->setVm($vm);
        $vmLogger = $logger->createLogger();

        $cmd = $this->getPrefixCommand($vm->getInteg(), $cmd);
        $process = new LoggableProcess($cmd);
        $process->setLogger($vmLogger);
        $process->setTimeout(0);
        $process->run(array('\LaFourchette\Logger\VmProcessLogFormatter', 'format'));

        $output = $process->getOutput();

        return $output;
        // @codeCoverageIgnoreEnd
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

        $cmd = $this->getPullVmCommand();
        $this->run($vm, $cmd);

        $this->generateFact($vm, $node);

        $cmd = 'vagrant up --no-provision';
        $this->run($vm, $cmd);

        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
            case VM::STOPPED:
            case VM::MISSING:
                throw new UnableToStartException('The Vm has not started');
            case VM::RUNNING:
                //TODO: nothing
                break;
        }

        if ($provisionEnable) {
            $cmd = 'vagrant provision';
            $this->run($vm, $cmd);
        }
    }

    /**
     * @codeCoverageIgnore
     * @param  VM     $vm
     * @return void
     */
    public function stop(VM $vm)
    {
        $cmd = 'vagrant halt --force';
        $this->run($vm, $cmd);
    }

    /**
     * @codeCoverageIgnore
     * @param  VM     $vm
     * @return void
     */
    public function initialise(VM $vm)
    {
        $cmd = $this->getCloneVmCommand();
        $this->run($vm, $cmd);

        $this->generateFact($vm);
    }

    private function getPullVmCommand()
    {
        return sprintf(self::GIT_PULL_VM_CMD, $this->defaultBranch);
    }


    private function getCloneVmCommand()
    {
        return sprintf(self::CLONE_VM_CMD, $this->repo, $this->defaultBranch);
    }

    protected function generateFact(Vm $vm, $node = 'integ.lafourchette.local')
    {
        $integ  = $vm->getInteg();
        $mac = str_replace(':', '', $integ->getMac());
        $netmask = $integ->getNetmask();

        $branches['branches_lafourchette_portal'] = 'master';
        $branches['branches_lafourchette_mailer'] = 'master';
        $branches['branches_lafourchette_module'] = 'master';
        $branches['branches_lafourchette_rr'] = 'master';
        $branches['branches_lafourchette_bo'] = 'master';
        $branches['branches_lafourchette_core'] = 'master';
        $branches['branches_lafourchette_webmobile'] = 'master';
        $branches['branches_lafourchette_b2b'] = 'master';
        $branches['branches_lafourchette_payment'] = 'master';
        $branches['branches_lafourchette_b2b_extranet'] = 'master';
        $branches['branches_lafourchette_b2brrapi'] = 'master';

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
                case 'lafourchette-payment':
                    $branches['branches_lafourchette_payment'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-b2b-extranet':
                    $branches['branches_lafourchette_b2b_extranet'] = $vmProject->getBranch();
                    break;
                case 'lafourchette-b2b-rr-api':
                    $branches['branches_lafourchette_b2brrapi'] = $vmProject->getBranch();
                    break;
               case 'lafourchette-b2b-stats':
                    $branches['branches_lafourchette_b2b-stats'] = $vmProject->getBranch();
                    break;
               case 'lafourchette-recovery':
                    $branches['branches_lafourchette_recovery'] = $vmProject->getBranch();
                    break;
            }
        }

        $suffix = $integ->getSuffix();
        $ip = $integ->getIp();
        $bridge = $integ->getBridge();
        $githubKey = $vm->getInteg()->getGithubKey();

        $fact = <<<EOS
Facts = {
  'facter' => {
    'application_env' => 'demo',
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
    'branches_lafourchette_b2b' => '{$branches['branches_lafourchette_b2b']}',
    'branches_lafourchette_payment' => '{$branches['branches_lafourchette_payment']}',
    'branches_lafourchette_b2b_extranet' => '{$branches['branches_lafourchette_b2b_extranet']}',
    'branches_lafourchette_b2brrapi' => '{$branches['branches_lafourchette_b2brrapi']}'
  },
  # Key used for cloning lf repos. Copied at VM startup
  'github_private_key' => '{$githubKey}',
  'node' => '{$node}',
  'debug' => false,
  'nfs' => false,
  'share' => false,
  'network_type' => 'public',
  'ip' => '{$ip}',
  'bridge' => '{$bridge}',
  'mac' => '{$mac}', # used only in public network
  'netmask' => '{$netmask}' # used only in public network
}
EOS;

        $cmd = 'echo "'.str_replace('"', '\"', $fact).'" > Facts';
        $this->run($vm, $cmd);
    }

    /**
     * @codeCoverageIgnore
     * @param  VM     $vm
     * @return void
     */
    public function reset(VM $vm)
    {
        $this->stop($vm);
        $this->start($vm);
    }

    /**
     * @codeCoverageIgnore
     * @param  VM     $vm
     * @return void
     */
    public function delete(VM $vm)
    {
        $cmd = 'vagrant halt --force';
        $this->run($vm, $cmd);

        $cmd = 'vagrant destroy -f';
        $this->run($vm, $cmd);
    }
}
