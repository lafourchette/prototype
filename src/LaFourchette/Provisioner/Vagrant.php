<?php
namespace LaFourchette\Provisioner;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\VM;
use LaFourchette\Manager\IntegManager;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use Symfony\Component\Process\Process;
use LaFourchette\Logger\LoggableProcess;
use LaFourchette\Logger\VmLogger;
use LaFourchette\Provisioner\Shell\GithubFile;
use LaFourchette\Provisioner\Shell\LocalFile;

class Vagrant implements ProvisionerInterface
{
    const CLONE_VM_CMD = 'git clone %s . && git checkout -t origin/%s';
    const GIT_PULL_VM_CMD = 'git fetch && git checkout %s && git pull';

    /**
     * @var string
     */
    protected $repo = '';

    protected $integManager;

    protected $defaultBranch = '';

    private $provisioners = array();

    /**
     * @param $id
     * @return Integ
     */
    public function getInteg($id)
    {
        return $this->integManager->load($id);
    }

    /**
     * @param IntegManager $integManager
     * @param $configurations { 'provisioners': ... } in the config.json
     */
    public function __construct(IntegManager $integManager, $configurations)
    {
        $this->integManager = $integManager;
        foreach ($configurations as $configuration) {
            if (! isset($configuration['type'])) {
                throw new \Exception('missing type key in provisioner configuration');
            }
            switch ($configuration['type']) {
                case 'local':
                    $p = new LocalFile($configuration['path']);
                    break;
                case 'github':
                    $p = new GithubFile(
                        $configuration['repository'],
                        $configuration['path'],
                        $configuration['token'],
                        $configuration['user']
                    );
                    break;
                default:
                    throw new \Exception('unknown provisioner type '.$configuration['type']);
            }
            array_push($this->provisioners, $p);
        }
    }

    /**
     * @param $integ
     * @param  string     $realCommand
     * @return string
     * @throws \Exception
     */
    protected function getPrefixCommand($integ, $realCommand, $prefix = true)
    {
        $cmd = '';
        $sshUser = $integ->getSshUser();
        $server = $integ->getNode()->getIp();

        if (trim($sshUser) != '' && trim($server) != '') {
            $encapsultate = 'ssh -o "StrictHostKeyChecking no" ' . $sshUser . '@' . $server . ' ';
        }

        if ($prefix) {
            $path = $integ->getPath();
            if (trim($path) !== '') {
                $cmd .= 'cd ' . $path . '; ';
            } else {
                throw new \Exception('Seriously ? no path ? I can deploy the VM everywhere ?');
            }
        }

        $cmd .= $realCommand;

        if (isset($encapsultate)) {
            $cmd = $encapsultate . ' "' . str_replace('"', '\"', $cmd) . '"';
        }

        return $cmd;
    }

    /**
     * @param VM $vm
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getStatus(VM $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $output = $this->run($vm, 'ls -a ' . $path, false);

        $result = explode("\n", $output);

        if (count($result) == 0) {
            throw new \Exception('Destination directory does not exists');
        } else {
            $output = $this->run($vm, 'vagrant status 2>&1');

            if (strpos($output, 'is required to run') !== false) {
                return VM::MISSING;
            } elseif (strpos($output, ' running (') !== false) {
                $now = new \DateTime();
                if ($now > $vm->getExpiredDt()) {
                    return VM::EXPIRED;
                }

                return VM::RUNNING;
            } elseif (strpos($output, ' not created (') !== false) {
                return VM::STOPPED;
            } elseif (strpos($output, ' poweroff (') !== false) {
                $now = new \DateTime();
                if ($now > $vm->getExpiredDt()) {
                    return VM::EXPIRED;
                }

                return VM::STOPPED;
            } elseif (strpos($output, ' saved (') !== false) {
                return VM::SUSPEND;
            } else {
                throw new \Exception('This is not normal ...');
            }
        }
    }

    /**
     * @param  VM     $vm
     * @param  string $cmd
     * @param bool
     * @return string
     */
    protected function run(VM $vm, $cmd, $prefix = true, $remote = true)
    {
        // @codeCoverageIgnoreStart
        $logger = new VmLogger();
        $logger->setVm($vm);
        $vmLogger = $logger->createLogger();

        if ($remote) {
            $cmd = $this->getPrefixCommand(
                $this->getInteg($vm->getInteg()),
                $cmd,
                $prefix
            );
        }

        echo $cmd . PHP_EOL;

        $process = new LoggableProcess($cmd);
        $process->setLogger($vmLogger);
        $process->setTimeout(0);
        $process->run(array('\LaFourchette\Logger\VmProcessLogFormatter', 'format'));

        $output = $process->getOutput();

        return $output;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param  VM                               $vm
     * @throws Exception\UnableToStartException
     */
    public function start(VM $vm, $provisionEnable = true, $node = 'integ.lafourchette.local')
    {
        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
            case VM::RUNNING:
                throw new \Exception('VM is already running');
                break;
            default:
                $this->initialise($vm);
                break;
        }

        $this->run($vm, 'vagrant up');

        switch ($this->getStatus($vm)) {
            case VM::SUSPEND:
            case VM::STOPPED:
            case VM::MISSING:
                throw new UnableToStartException('The Vm has not started');
            case VM::RUNNING:
                //TODO: nothing
                break;
        }
    }

    /**
     * @codeCoverageIgnore
     * @param VM $vm
     *
     * @return void
     */
    public function stop(VM $vm)
    {
        $this->run($vm, 'vagrant halt --force');
    }

    /**
     * @codeCoverageIgnore
     * @param VM $vm
     *
     * @return void
     */
    public function initialise(VM $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "mkdir -p $path", false);
        $this->cleanUp($vm);

        $version = file_get_contents('http://par-resources.us.tripadvisor.local/current/phing_lucid.VERSION');
        if (!$version) {
            throw new \Exception('Cannot find a suitable version to run');
        }

        $this->generateVagrantfile($vm, trim($version));
        $this->generateInstallScript($vm);
    }

    private function cleanUp(VM $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "rm -rf $path/*; rm -rf $path/.*", false);
    }

    /**
     * @param VM $vm
     * @see https://github.com/lafourchette/lafourchette-packer/blob/master/shared/guest_scripts/install.sh
     */
    private function generateInstallScript(VM $vm)
    {
        $integ   = $this->getInteg($vm->getInteg());

        foreach ($this->provisioners as $provisioner) {
            $installScript = preg_replace_callback(
                '#\{?\$\{?([^\}\{]*)\}#',
                function ($matches) use ($integ) {
                    switch ($matches[1]) {
                        case 'ip':
                            return $integ->getIp();
                            break;
                        case 'netmask':
                            return $integ->getNetmask();
                            break;
                        case 'suffix':
                            return $integ->getSuffix();
                            break;
                        case 'dotlessSuffix':
                            return substr($integ->getSuffix(), 1);
                            break;
                        default:
                            throw new \Exception('unknown parameter '.$matches[0]);
                    }
                },
                $provisioner->getContent()
            );

            $this->sendfile($vm, 'install.sh', $installScript);
        }
    }

    /**
     * Creates a Vagrantfile on the host for the guest VM
     * @see Inspired on https://github.com/lafourchette/lafourchette-vm/blob/2.0/Vagrantfile
     */
    private function generateVagrantfile(VM $vm, $version)
    {
        $integ   = $this->getInteg($vm->getInteg());
        $mac     = str_replace(':', '', $integ->getMac());
        $netmask = $integ->getNetmask();
        $ip      = $integ->getIp();
        $bridge  = $integ->getBridge();

        $vagrantFile = <<<EOS
# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
    config.vm.box = "{$version}"
    config.vm.box_url = "http://par-resources.us.tripadvisor.local/current/phing_lucid.box"
    config.vm.provider :virtualbox do |vb|
        vb.customize ["modifyvm", :id, "--natdnshostresolver1", "off"]
        vb.customize ["modifyvm", :id, "--natdnsproxy1", "off"]
        vb.customize ["modifyvm", :id, "--memory", "16384"]
        vb.customize ["modifyvm", :id, "--cpus", "8"]
    end
    config.vm.provision "shell", path: "install.sh"
EOS;

        if ($ip && $mac && $netmask && $bridge) {
            $vagrantFile.= <<<EOS
    # Network configuration
    config.vm.network :public_network, ip: '{$ip}', :bridge => '{$bridge}',  :mac => '{$mac}', :netmask => '{$netmask}'
    # Default router
    config.vm.provision "shell",
    run: "always",
    inline: "route add default gw 10.131.198.1"
     # delete default gw on eth0
    config.vm.provision "shell",
    run: "always",
    inline: "eval `route -n | awk '{ if ($8 ==\"eth0\" && $2 != \"0.0.0.0\") print \"route del default gw \" $2; }'`"
end
EOS;
        } else {
            $vagrantFile.= <<<EOS
    # Network configuration
    config.vm.network :private_network, ip: '192.168.33.33'
end
EOS;
        }

        $this->sendfile($vm, 'Vagrantfile', $vagrantFile);
    }

    /**
     * @codeCoverageIgnore
     * @param VM $vm
     *
     * @return void
     */
    public function reset(VM $vm)
    {
        throw new \Exception('This is not supported. Delete the VM.');
    }

    /**
     * @codeCoverageIgnore
     * @param VM $vm
     *
     * @return void
     */
    public function delete(VM $vm)
    {
        $this->run($vm, 'vagrant halt --force');
        $this->run($vm, 'vagrant destroy -f');
        $this->cleanUp($vm);
    }

    /**
     * Send a file to the server VMs path.
     */
    private function sendfile(Vm $vm, $file, $content)
    {
        // Create a temp file with content
        $tmpfname = tempnam(sys_get_temp_dir(), "FOO");
        if (!$tmpfname) {
            throw new \Exception('cannot create tempfile');
        }
        file_put_contents($tmpfname, $content);

        $integ   = $this->getInteg($vm->getInteg());
        $sshUser = $integ->getSshUser();
        $server  = $integ->getNode()->getIp();
        $path    = $integ->getPath();

        if (trim($sshUser) != '' && trim($server) != '') {
            $cmd = sprintf(
                'scp -o "StrictHostKeyChecking no" %s %s@%s:%s',
                $tmpfname,
                $sshUser,
                $server,
                $path.'/'.$file
            );
        } else {
            $cmd = sprintf(
                'cp %s %s',
                $tmpfname,
                $path.'/'.$file
            );
        }

        $this->run($vm, $cmd, false, false);

        unlink($tmpfname);
    }
}
