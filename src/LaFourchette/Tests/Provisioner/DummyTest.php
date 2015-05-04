<?php

namespace LaFourchette\Tests\Provisioner;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\Vm;
use LaFourchette\Provisioner\Dummy;

class DummyTest extends \PHPUnit_Framework_Testcase
{
    protected $provisioner;

    public function setUp()
    {
        $integ = new Integ();
        $integ->setPath('Fake/path');

        $this->provisioner = new Dummy($this->getIntegManagerMock($integ));
    }

    public function testGetStatusIsRunning()
    {
        $this->assertEquals(Vm::RUNNING, $this->provisioner->getStatus(new Vm()));
    }

    public function testGetStatusIsExpired()
    {
        $vm = new Vm();
        $vm->setExpiredDt(new \DateTime('-1 day'));

        $this->assertEquals(Vm::EXPIRED, $this->provisioner->getStatus($vm));
    }

    public function testInitialise()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testStart()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testStop()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage This is not supported. Delete the VM.
     */
    public function testReset()
    {
        $this->provisioner->reset(new Vm());
    }

    protected function getIntegManagerMock($integ = null)
    {
        $stub = $this->getMockBuilder('LaFourchette\Manager\IntegManager')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('load')->willReturn($integ);

        return $stub;
    }
}
