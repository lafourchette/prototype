<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('My Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

\LaFourchette\Console\Status::register($app, $console);
\LaFourchette\Console\Check::register($app, $console);
\LaFourchette\Console\Create::register($app, $console);
\LaFourchette\Console\Delete::register($app, $console);
\LaFourchette\Console\Reset::register($app, $console);

return $console;
