<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prototype:start')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Start a VM');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');
        $vmManager = $this->getVmManager();

        $vm = $vmManager->load($vmNumber);

        $this->getSilexApplication()['vm.service']->start($vm);
    }
}