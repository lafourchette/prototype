<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');

        $vmManager = $this->getVmManager();

        $vm = $vmManager->load($vmNumber);

        if ($vm !== null) {
            $vms = array($vm);
        } else {
            throw new \InvalidArgumentException('The given Vm could not be found');
        }

        $notify = $this->getNotify();

        $output->writeln('Start the checks of all VM');

        foreach ($vms as $vm) {
            /** @var VM $vm */
            $output->writeln('> VM ' . $vm->__toString());
            $savedStatus = $vm->getStatus();
            $currentStatus = $this->getSilexApplication()['vm.service']->getStatus($vm);

            if (is_null($currentStatus)) {
                $output->writeln('cannot resolve status for vm ' . $vm->getIdVm());
                continue;
            }

            $output->writeln('  - Old status: ' . $savedStatus);
            $output->writeln('  - Current status: ' . $currentStatus);

            if ($savedStatus == Vm::TO_START && $currentStatus != Vm::RUNNING) {
                $output->writeln('  - Need to be started');
                $output->writeln('  - Do it Now');
                $this->getSilexApplication()['vm.service']->start($vm);
            } elseif ($savedStatus == Vm::ARCHIVED) {
                $output->writeln('  - Vm is archived.');
            } elseif ($savedStatus == Vm::EXPIRED) {
                $output->writeln('  - Has just expired');
                $notify->send('expired', $vm);
                $this->getSilexApplication()['vm.service']->archived($vm);
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
                            $expireDt->add(new \DateInterval('PT'.$this->getSilexApplication()['config']['vm.to_expire_in'].'H'));
                            if ($expireDt > new \DateTime()) {
                                $notify->send('expire_soon', $vm);
                            }
                            break;
                        case Vm::STOPPED:
                            if ($savedStatus != Vm::STOPPED && $savedStatus != Vm::EXPIRED && $savedStatus != Vm::ARCHIVED) {
                                $output->writeln('  - Has been just killed');
                                //Someone else have killed the VM (serveur ? admin ? other ?) Something wrong append
                                $notify->send('killed', $vm);
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
}
