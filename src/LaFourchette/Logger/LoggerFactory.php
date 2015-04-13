<?php

namespace LaFourchette\Logger;

use LaFourchette\Logger\VmLogger;
use LaFourchette\Logger\DummyLogger;

class LoggerFactory
{
    const LOGGER_VM = 'VmLogger';
    const LOGGER_DUMMY = 'DummyLogger';

    /**
     * @param $loggerType
     * @return DummyLogger|VmLogger
     * @throws \Exception
     */
    public static function create($loggerType)
    {
        if (trim($loggerType) === '') {
            throw new \Exception('Type de logger manquant');
        }

        switch (trim($loggerType)) {
            case self::LOGGER_VM:
                $logger = new VmLogger();
                break;
            case self::LOGGER_DUMMY:
                $logger = new DummyLogger();
                break;
            default:
                throw new \Exception('Type de logger inconnu');
                break;
        }

        return $logger->createLogger();
    }
}
