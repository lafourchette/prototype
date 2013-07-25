<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetVmId extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:get-vm-id')
            ->setDescription('Get all VM Id')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new GetVmId();
                $command->setApplication($app);
                $command->run($input, $output);
            });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $vmManager = $this->getVmManager();

        $status = Vm::$availableStatus;
        $status[] = Vm::ARCHIVED;

        $vms = $vmManager->loadBy(array('status' => $status));

        foreach ($vms as $vm) {
            $output->writeln($vm->getIdVm());
        }
    }
}