<?php

namespace LaFourchette\Tests\Logger;

use LaFourchette\Logger\LoggerFactory;

class LoggerFactoryTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Type de logger manquant
     */
    public function testCreateWithoutLoggerTypeWillThrowException()
    {
        LoggerFactory::create(null);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Type de logger inconnu
     */
    public function testCreateWithUnknownLoggerTypeWillThrowException()
    {
        LoggerFactory::create('poulp');
    }

    public function testCreateWillReturnDummyLogger()
    {
        $this->assertInstanceOf('\MonoLog\Logger', LoggerFactory::create(LoggerFactory::LOGGER_DUMMY));
    }
}
