<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prototype:status')
        ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
        ->setDescription('Check the status of a VM');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');
        $vmManager = $this->getVmManager();

        /**
         * @var VM $vm
         */
        $vm = $vmManager->load($vmNumber);

        switch ($this->getSilexApplication()['vm.service']->getStatus($vm)) {
            case VM::MISSING:
                $output->writeln('The VM is missing');
                break;
            case VM::RUNNING:
                $output->writeln('The VM is running');
                break;
            case VM::STOPPED:
                $output->writeln('The VM is stopped');
                break;
            case VM::SUSPEND:
                $output->writeln('The VM is suspend');
                break;
        }
    }
}