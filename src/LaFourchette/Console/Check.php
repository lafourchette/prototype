<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Check extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prototype:check')
            ->setDescription('Check all state of VM')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'The vm number')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function process(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getOption('id');
        $vmManager = $this->getVmManager();

        if ($vmNumber) {
            $vm = $vmManager->load($vmNumber);
            if ($vm !== null) {
                $vms = array($vm);
            } else {
                throw new \InvalidArgumentException('The given Vm could not be found');
            }
        } else {
            $status = Vm::$availableStatus;
            $status[] = VM::EXPIRED;

            $vms = $vmManager->loadBy(array('status' => $status));
        }

        foreach ($vms as $vm) {
            $this->check($vmManager, $vm, $output);
        }
    }

    public function check(VmManager $vmManager, Vm $vm, OutputInterface $output)
    {
        /** @var VM $vm */
        $output->writeln('> VM ' . $vm->__toString());
        $savedStatus = $vm->getStatus();
        $silexApp = $this->getSilexApplication();
        $currentStatus = $silexApp['vm.service']->getStatus($vm);

        if (is_null($currentStatus)) {
            $output->writeln( 'Cannot resolve status for vm ' . $vm->getIdVm());
            return;
        }

        $output->writeln('  - Old status: ' . $savedStatus . '  => Current status: ' . $currentStatus);

        if ($savedStatus == Vm::TO_START && $currentStatus != Vm::RUNNING) {
            $output->writeln('  - Need to be started, Do it Now');
            $silexApp['vm.service']->start($vm);
        } elseif ($savedStatus == Vm::ARCHIVED) {
            $output->writeln('  - Vm is archived.');
        } elseif ($savedStatus == Vm::EXPIRED) {
            $output->writeln('  - Has just expired');
            $silexApp['vm.service']->archived($vm);
            $vm->setStatus(Vm::ARCHIVED);
            $vmManager->save($vm);
        } else {
            if ($savedStatus != $currentStatus) {
                $vm->setStatus($currentStatus);
                $vmManager->save($vm);
                switch ($currentStatus) {
                    case Vm::RUNNING:
                        $output->writeln('  - Running');
                        $expireDt = $vm->getExpiredDt();
                        $expireDt->add(new \DateInterval('PT'.$silexApp['config']['vm.to_expire_in'].'H'));
                        break;
                    case Vm::STOPPED:
                        if ($savedStatus != Vm::STOPPED && $savedStatus != Vm::EXPIRED && $savedStatus != Vm::ARCHIVED) {
                            $output->writeln('  - Has been just killed');
                            //Someone else have killed the VM (serveur ? admin ? other ?) Something wrong append
                        }
                        break;
                    case Vm::SUSPEND:
                        //todo: this case is currently not used
                        break;
                    case Vm::MISSING:
                        $output->writeln('  - Is missing');
                        break;
                }
            }
        }
    }
}
