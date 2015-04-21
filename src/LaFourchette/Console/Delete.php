<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prototype:delete')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Delete a VM');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');
        $vmManager = $this->getVmManager();

        /**
         * @var Vm $vm
         */
        $vm = $vmManager->load($vmNumber);

        $this->getSilexApplication()['vm.service']->delete($vm);
    }
}