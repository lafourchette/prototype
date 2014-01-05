<?php

namespace LaFourchette\Prototype\Tests\Provisionner;

class VagrantProvisionnerTest extends \PHPUnit_Framework_Testcase
{

    public function testMissingFactFile()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('git@github.com:lafourchette/lafourchette-vm', '1.2.0'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('Please update Facts file !'));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testVmNotCreated()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' not created ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testPowerOffNonExpiredVm()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  poweroff ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))));

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::STOPPED, $status);
    }

    public function testPowerOffExpiredVm()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  poweroff ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime('2014-01-01 10:10:10'));

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::EXPIRED, $status);
    }

    public function testRunningNonExpiredVm()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' running ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))));

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::RUNNING, $status);
    }

    public function testRunningExpiredVm()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue(' running ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $vm->setExpiredDt(new \DateTime('2014-01-01 10:10:10'));

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::EXPIRED, $status);
    }

    public function testSupendedVm()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue('  saved ('));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);

        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::SUSPEND, $status);
    }    

    public function testMissingVmFiles()
    {
        $return = <<<EOF
        .
        ..

EOF;

        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run'), array('depot', 'branch'));
        $vagrantProvisionnerMock
            ->expects($this->any())
            ->method('run')
            ->will($this->returnValue($return));

        $vm = new \LaFourchette\Entity\Vm();
        $integ = new \LaFourchette\Entity\Integ();
        $integ->setPath('/test');
        $vm->setInteg($integ);
        $status = $vagrantProvisionnerMock->getStatus($vm);

        $this->assertEquals(\LaFourchette\Entity\Vm::MISSING, $status);
    }

    public function testStartSupendWorkflow()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run','getStatus'), array('depot', 'branch'));
        $vmMock = $this->getMock('\LaFourchette\Entity\Vm');
        $vagrantProvisionnerMock->expects($this->any())
             ->method('getStatus')
             ->with($vmMock)
             ->will($this->returnValue(\LaFourchette\Entity\Vm::SUSPEND));

        //First case, cannot supend the vm if it's already running
        $this->setExpectedException('Exception', 'VM is already running');
        $vagrantProvisionnerMock->start($vmMock);    

    }

    public function testStartRunningWorkflow()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run', 'initialise', 'generateFact', 'getStatus'), array('depot', 'branch'));
        $vmMock = $this->getMock('\LaFourchette\Entity\Vm');
        $vagrantProvisionnerMock->expects($this->any())
             ->method('getStatus')
             ->with($vmMock)
             ->will($this->returnValue(\LaFourchette\Entity\Vm::RUNNING));

        //First case, cannot supend the vm if it's already running
        $this->setExpectedException('Exception', 'VM is already running');
        $vagrantProvisionnerMock->start($vmMock);    

    }

    public function testStartMissingWorkflow()
    {
        $vagrantProvisionnerMock = $this->getMock('\LaFourchette\Provisioner\Vagrant', array('__construct', 'run', 'initialise', 'generateFact', 'getStatus'), array('depot', 'branch'));
        $vmMock = $this->getMock('\LaFourchette\Entity\Vm');
        $vagrantProvisionnerMock->expects($this->any())
             ->method('getStatus')
             ->with($vmMock)
             ->will($this->returnValue(\LaFourchette\Entity\Vm::MISSING));

        //First case, cannot supend the vm if it's already running
        $this->setExpectedException('Exception', 'The Vm have not started');
        $vagrantProvisionnerMock->start($vmMock);    

    }    
}