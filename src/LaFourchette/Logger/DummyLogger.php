<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 11/04/2015
 * Time: 10:30
 */

namespace LaFourchette\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DummyLogger
{
    const LOG_FILE_MASK = 'dummy-file';

    /**
     * @return Logger
     */
    public function createLogger()
    {
        $logger = new Logger('dummy-channel' . date('Y-m-d'));
        $handler = new StreamHandler(self::getLogFile(), Logger::INFO);
        //[%datetime%] : %message% %context% %extra%\n
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] : %message%", 'Y-m-d H:i:s', true));
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param null $logDir
     * @return string
     */
    public static function getLogFile($logDir = null)
    {
        if (!$logDir) {
            $logDir = __DIR__ . '/../../../logs';
        }

        return $logDir . '/' . self::LOG_FILE_MASK . '.log';
    }
}