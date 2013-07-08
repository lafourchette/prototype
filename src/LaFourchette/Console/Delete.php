<?php

namespace LaFourchette\Console;

use LaFourchette\Provisioner\Vagrant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:delete')
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Delete a VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Delete();
                $command->setApplication($app);
                $command->run($input, $output);
            });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');
        $vmManager = $this->getVmManager();

        /**
         * @var VM $vm
         */
        $vm = $vmManager->load($vmNumber);

        $provisioner = $this->getProvisioner();
        $provisioner->delete($vm);
    }
}