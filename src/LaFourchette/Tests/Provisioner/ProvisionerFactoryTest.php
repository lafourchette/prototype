<?php

namespace LaFourchette\Tests\Provisioner;

use LaFourchette\Provisioner\ProvisionerFactory;

class ProvisionerFactoryTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Undefined provisioner of type fake
     */
    public function testCreateWillThrowUnexpectedValueException()
    {
        ProvisionerFactory::create('fake');
    }

    public function testCreateWillReturnVagrantProvisioner()
    {

        $this->assertInstanceOf(
            'LaFourchette\Provisioner\Vagrant',
            ProvisionerFactory::create(
                ProvisionerFactory::PROVISIONER_VAGRANT,
                $this->getIntegManagerMock()
            )
        );
    }

    public function testCreateWillReturnDummyProvisioner()
    {
        $this->assertInstanceOf(
            'LaFourchette\Provisioner\Dummy',
            ProvisionerFactory::create(
                ProvisionerFactory::PROVISIONER_DUMMY,
                $this->getIntegManagerMock()
            )
        );
    }

    private function getIntegManagerMock()
    {
        return $this->getMockBuilder('LaFourchette\Manager\IntegManager')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
