<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\VM;
use LaFourchette\Logger\LoggableProcess;
use LaFourchette\Logger\VmLogger;

abstract class ProvisionerAbstract implements ProvisionerInterface
{
    protected $integManager;

    /**
     * @param $id
     * @return Integ
     */
    public function getInteg($id)
    {
        return $this->integManager->load($id);
    }
    /**
     * @param VM $vm
     * @param string $cmd
     * @param bool
     * @return string
     */
    protected function run(VM $vm, $cmd, $prefix = true, $remote = true)
    {
        // @codeCoverageIgnoreStart
        $logger = new VmLogger();
        $logger->setVm($vm);
        $vmLogger = $logger->createLogger();

        if($remote){
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
     * @param $integ
     * @param string $realCommand
     * @return string
     * @throws \Exception
     */
    protected function getPrefixCommand(Integ $integ, $realCommand, $prefix = true)
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
}