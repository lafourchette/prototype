<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\VM;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provisioner\Vagrant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     * @return mixed|void
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:status')
        ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
        ->setDescription('Check the status of a VM')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            $command = new Status();
            $command->setApplication($app);
            $command->run($input, $output);
        });
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed|void
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');
        $vmManager = $this->getVmManager();

        /**
         * @var VM $vm
         */
        $vm = $vmManager->load($vmNumber);

        switch ($this->application['vm.service']->getStatus($vm)) {
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