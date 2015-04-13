<?php

namespace LaFourchette\Tests\Logger;

use LaFourchette\Logger\DummyLogger;

class DummLoggerTest extends \PHPUnit_Framework_Testcase
{
    public function testCreateLoggerWillReturnDummyLogger()
    {
        $dummyLogger = new DummyLogger();
        $logger = $dummyLogger->createLogger();
        $this->assertSame('dummy-channel' . date('Y-m-d'), $logger->getName());
    }

    public function testGetLogFileWithoutLogDir()
    {
        $dummyLogger = new DummyLogger();
        $this->assertContains(
            DummyLogger::LOG_FILE_MASK . '.log',
            DummyLogger::getLogFile()
        );
    }

    public function testGetLogFileWithLogDir()
    {
        $this->assertSame(
            'poulpLogs' . '/' . DummyLogger::LOG_FILE_MASK . '.log',
            DummyLogger::getLogFile('poulpLogs')
        );
    }
}
