<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Entity\VM;
use LaFourchette\Logger\DummyLogger;
use LaFourchette\Logger\LoggerFactory;
use LaFourchette\Logger\LoggableProcess;

class Dummy implements ProvisionerInterface
{

    const TYPE_DEFAULT = 99;

    protected $logger;

    public function getStatus(VM $vm)
    {
        return new \Exception(__METHOD__ . ' not implemented in this context for ' . __CLASS__);
    }

    /**
     * @param VM $vm
     * @return mixed
     */
    public function start(VM $vm)
    {
        $dummyLogger = LoggerFactory::create(LoggerFactory::LOGGER_DUMMY);

        $cmd = 'echo dummy provisioner start';

        echo $cmd . PHP_EOL;

        $process = new LoggableProcess($cmd);
        $process->setLogger($dummyLogger);
        $process->setTimeout(0);
        $process->run(array('\LaFourchette\Logger\DummyProcessLogFormatter', 'format'));

        return $process->getOutput();
    }

    public function stop(VM $vm)
    {
        $dummyLogger = LoggerFactory::create(LoggerFactory::LOGGER_DUMMY);

        $cmd = 'echo dummy provisioner stop';

        echo $cmd . PHP_EOL;

        $process = new LoggableProcess($cmd);
        $process->setLogger($dummyLogger);
        $process->setTimeout(0);
        $process->run(array('\LaFourchette\Logger\DummyProcessLogFormatter', 'format'));

        return $process->getOutput();
    }

    public function initialise(VM $vm)
    {
        return new \Exception(__METHOD__ . ' not implemented in this context for ' . __CLASS__);
    }

    public function reset(VM $vm)
    {
        return new \Exception(__METHOD__ . ' not implemented in this context for ' . __CLASS__);
    }

    public function delete(VM $vm)
    {
        return new \Exception(__METHOD__ . ' not implemented in this context for ' . __CLASS__);
    }
}
