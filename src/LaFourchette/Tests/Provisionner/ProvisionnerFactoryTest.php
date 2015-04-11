<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 11/04/2015
 * Time: 16:20
 */

namespace LaFourchette\Tests\Provisionner;


use LaFourchette\Provisioner\ProvisionerFactory;

class ProvisionnerFactoryTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Type de provisioner manquant
     */
    public function testCreateWillThrowExceptionMissingProvisioner()
    {
        ProvisionerFactory::create(null);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Type de provisioner inconnu
     */
    public function testCreateWillThrowExceptionUnknownProvisioner()
    {
        ProvisionerFactory::create('poulp');
    }

    public function testCreateWillReturnDummyProvisioner()
    {
        $this->assertInstanceOf(
            "LaFourchette\Provisioner\Dummy",
            ProvisionerFactory::create(ProvisionerFactory::PROVISIONER_DUMMY)
        );
    }
}