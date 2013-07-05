<?php

namespace LaFourchette\Console;

use LaFourchette\Model\Integ;
use LaFourchette\Model\VM;
use LaFourchette\Provisioner\Vagrant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:create')
            ->setDefinition(array(
                new InputOption('vm', null, InputOption::VALUE_NONE, 'Some help'),
            ))
            ->setDescription('Create a VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Create();
                $command->setApplication($app);
                $command->run($input, $output);
            });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $vm = new VM();
        $integ = new Integ();

        $integ->setName('test');
        $integ->setSuffix('test');
        $integ->setPath('/home/laurent_chenay/www/lafourchette-prototype-test');
        $integ->setServer(null);
        $integ->setSshKey(null);
        $integ->setSshUser(null);
        $integ->setIp(null);
        $integ->setMac(null);

        $vm->setInteg($integ);

        $vagrant = new Vagrant();

        $vagrant->start($vm);
    }
}