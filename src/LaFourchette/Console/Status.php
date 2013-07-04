<?php

namespace LaFourchette\Console;

use Symfony\Component\Console\Application;
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
            // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
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
        $output->writeln('Status');
    }
}