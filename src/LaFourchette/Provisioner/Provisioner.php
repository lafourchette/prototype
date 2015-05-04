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
     * @return \LaFourchette\Entity\Integ
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
            $wrappedCmd = 'ssh -o "StrictHostKeyChecking no" ' . $sshUser . '@' . $server . '  "%s"';
        }

        return sprintf($wrappedCmd, str_replace('"', '\"', $cmd . $realCommand));
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
            $cmd = $this->getPrefixCommand($this->getInteg($vm->getInteg()), $cmd, $prefix);
        }

        echo $cmd . PHP_EOL;

        $logger = new VmLogger($vm);
        $process = new LoggableProcess($cmd);

        $process
            ->setLogger($logger->createLogger())
            ->setTimeout(0)
            ->run(array('\LaFourchette\Logger\VmProcessLogFormatter', 'format'));

        return $process->getOutput();
        // @codeCoverageIgnoreEnd
    }
}
