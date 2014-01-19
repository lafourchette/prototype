<?php

namespace LaFourchette\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class LoggableProcess extends Process
{
    /** @var LoggerInterface */
    public $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function run($callable = null)
    {
        $that = $this;

        return parent::run(
            function ($type, $data) use ($callable, $that) {
                if (null !== $callable) {
                    $data = call_user_func($callable, $type, $data);
                }

                if (null === $that->logger) {
                    return;
                }

                $that->logger->info($data, array(
                    'type' => $type,
                    'cmd' => $that->getCommandLine(),
                    'procid' => $that->getPid(),
                ));
            }
        );
    }
}
