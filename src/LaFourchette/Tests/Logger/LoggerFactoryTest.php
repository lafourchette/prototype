<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 11/04/2015
 * Time: 16:39
 */

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