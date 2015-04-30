<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetVmId extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prototype:get-vm-id')
            ->setDescription('Get all VM Id');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vmManager = $this->getVmManager();

        $status = Vm::$availableStatus;
        $status[] = VM::EXPIRED;

        $vms = $vmManager->loadBy(array('status' => $status));

        foreach ($vms as $vm) {
            $output->writeln($vm->getIdVm());
        }
    }
}
