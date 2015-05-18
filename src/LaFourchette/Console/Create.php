<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prototype:create')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Create a VM')
        ;
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
        $app = $this->getSilexApplication();
        $app['vm.service']->initialise($vm);
    }
}
