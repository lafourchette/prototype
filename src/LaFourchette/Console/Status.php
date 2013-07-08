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
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:status')
        ->setDefinition(array(
            new InputArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number'),
        ))
        ->setDescription('Check the status of a VM')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            $command = new Status();
            $command->setApplication($app);
            $command->run($input, $output);
        });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
//        $vm = new VM();
//        $integ = new Integ();
//
//        $integ->setName('test');
//        $integ->setSuffix('test');
//        $integ->setPath('/home/laurent_chenay/www/lafourchette-prototype-test');
//        $integ->setServer(null);
//        $integ->setSshKey(null);
//        $integ->setSshUser(null);
//        $integ->setIp(null);
//        $integ->setMac(null);
//
//        $vm->setInteg($integ);

        $app = $this->getApplication();

        /**
         * @var VmManager $vmMananger
         */

        $vmMananger = $app['vm.manager'];

        /**
         * @var VM $vm
         */
        $vm = $vmMananger->load(1);

        $vagrant = new Vagrant();
        switch ($vagrant->getStatus($vm)) {
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