<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\Vm;
use LaFourchette\Provisioner\Vagrant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     * @return mixed|void
     */
    public static function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:start')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Start a VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Start();
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

        $vm = $vmManager->load($vmNumber);

        $this->application['vm.service']->start($vm);
    }
}
