<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 11/04/2015
 * Time: 15:42
 */

namespace LaFourchette\Tests\Provisionner;

use LaFourchette\Entity\Vm;
use LaFourchette\Provisioner\Dummy;

class DummyProvisionerTest extends \PHPUnit_Framework_Testcase
{
    public function testStart()
    {
        $dummyProvisionner = new Dummy();
        $output = $dummyProvisionner->start(new Vm());

        $this->assertSame('dummy provisioner start' . PHP_EOL, $output);
    }

    public function testStop()
    {
        $dummyProvisionner = new Dummy();
        $output = $dummyProvisionner->stop(new Vm());

        $this->assertSame('dummy provisioner stop' . PHP_EOL, $output);
    }
}