<?php

namespace LaFourchette\Console;

use LaFourchette\Provisioner\Vagrant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stop extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     * @return mixed|void
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:stop')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Stop a VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Stop();
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

        $this->application['vm.service']->stop($vm);
    }
}