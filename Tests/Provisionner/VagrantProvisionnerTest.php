<?php

namespace LaFourchette\Prototype\Tests\Provisionner;

class VagrantProvisionnerTest extends \PHPUnit_Framework_Testcase
{

    public function testFactFile()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('git@github.com:lafourchette/lafourchette-vm', '1.2.0'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('Please update Facts file !'));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testVmNotCreated()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' not created ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testPowerOffNonExpiredVm()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  poweroff ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))));

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testPowerOffExpiredVm()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  poweroff ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime('2014-01-01 10:10:10'));

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::EXPIRED, $status);
    }

    public function testRunningNonExpiredVm()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' running ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))));

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::RUNNING, $status);
    }

    public function testRunningExpiredVm()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' running ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime('2014-01-01 10:10:10'));

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::EXPIRED, $status);
    }

    public function testSupendedVm()
    {
        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  saved ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::SUSPEND, $status);
    }    

    public function testMissingVmFiles()
    {
        $return = <<<EOF
        .
        ..

EOF;

        $vagrantProvisionner = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionner
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue($return));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $status = $vagrantProvisionner->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::MISSING, $status);
    }
}