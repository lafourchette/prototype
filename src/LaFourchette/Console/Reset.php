<?php

namespace LaFourchette\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reset extends ConsoleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prototype:reset')
            ->setDefinition(array(
                // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
            ))
            ->setDescription('Reset a VM');
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
        $this->getSilexApplication()['vm.service']->stop($vm);
        $this->getSilexApplication()['vm.service']->start($vm);
    }
}