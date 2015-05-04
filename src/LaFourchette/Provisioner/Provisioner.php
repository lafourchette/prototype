<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\Vm;
use LaFourchette\Logger\LoggableProcess;
use LaFourchette\Logger\VmLogger;
use LaFourchette\Manager\IntegManager;

abstract class Provisioner implements ProvisionerInterface
{
    /**
     * @var \LaFourchette\Manager\IntegManager
     */
    protected $integManager;

    public function __construct(IntegManager $integManager)
    {
        $this->integManager = $integManager;
    }

    /**
     * Get integ instance based on its id
     *
     * @param $id integ idenfifier
     *
     * @return null|\LaFourchette\Entity\Integ
     */
    public function getInteg($id)
    {
        return $this->integManager->load($id);
    }

    /**
     * Initialize VM helper
     *
     * @param Vm $vm
     */
    public function initialise(Vm $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "mkdir -p $path", false);
        $this->cleanUp($vm);
    }

    /**
     * Check if the targeted Vm has a valid destination directory
     *
     * @param Vm $vm
     */
    public function checkForDestinationDirectory(Vm $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $output = $this->run($vm, 'ls -a ' . $path, false);

        $result = explode("\n", $output);

        if (empty($result)) {
            throw new \Exception('Destination directory does not exists');
        }

        return;
    }

    /**
     * Delete Vm helper
     *
     * @param Vm $vm
     */
    public function delete(Vm $vm)
    {
        $this->cleanUp($vm);
    }

    /**
     * Cleanup Vm helper
     *
     * @param Vm $vm
     */
    private function cleanUp(Vm $vm)
    {
        $path = $this->getInteg($vm->getInteg())->getPath();
        $this->run($vm, "rm -rf $path/*; rm -rf $path/.*", false);
    }

    /**
     * @param Integ   $integ
     * @param string  $realCommand
     * @param boolean $prefix
     *
     * @return string
     * @throws \Exception
     */
    protected function getPrefixCommand(Integ $integ, $realCommand, $prefix = true)
    {
        $cmd = '';

        if ($prefix) {
            if ('' === trim($integ->getPath())) {
                throw new \Exception('Seriously ? no path ? I can deploy the VM everywhere ?');
            }

            $cmd .= 'cd ' . $integ->getPath() . '; ';
        }

        if ('' !== trim($sshUser = $integ->getSshUser()) && '' !==  trim($server = $integ->getNode()->getIp())) {
            return sprintf(
                'ssh -o "StrictHostKeyChecking no" ' . $sshUser . '@' . $server . '  "%s"',
                str_replace('"', '\"', $cmd . $realCommand)
            );
        }

        return $cmd . $realCommand;
    }

    /**
     * @param Vm     $vm
     * @param string $cmd
     * @param bool   $prefix
     * @param bool   $remote
     *
     * @return string
     */
    protected function run(Vm $vm, $cmd, $prefix = true, $remote = true)
    {
        // @codeCoverageIgnoreStart
        if ($remote) {
            $integ = $this->getInteg($vm->getInteg());

            if ($integ instanceof Integ) {
                $cmd = $this->getPrefixCommand($integ, $cmd, $prefix);
            }
        }

        //echo $cmd . PHP_EOL; @todo Why do we need to show the command to execute?

        $logger = new VmLogger($vm);
        $process = new LoggableProcess($cmd);

        $process
            ->setLogger($logger->createLogger())
            ->setTimeout(0)
            ->run(array('\LaFourchette\Logger\VmProcessLogFormatter', 'format'));

        return $process->getOutput();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Send a file to the server VMs path.
     */
    protected function sendfile(Vm $vm, $file, $content)
    {
        // Create a temp file with content
        $tmpfname = tempnam(sys_get_temp_dir(), "FOO");

        if (!$tmpfname) {
            throw new \Exception('cannot create tempfile');
        }

        file_put_contents($tmpfname, $content);

        $integ   = $this->getInteg($vm->getInteg());

        if ('' !== trim($integ->getSshUser()) && '' !== trim($integ->getNode()->getIp())) {
            $cmd = sprintf(
                'scp -o "StrictHostKeyChecking no" %s %s@%s:%s',
                $tmpfname,
                $integ->getSshUser(),
                $integ->getNode()->getIp(),
                $integ->getPath().'/'.$file
            );
        } else {
            $cmd = sprintf(
                'cp %s %s',
                $tmpfname,
                $integ->getPath().'/'.$file
            );
        }

        $this->run($vm, $cmd, false, false);

        unlink($tmpfname);
    }
}
