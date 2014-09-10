<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$title = <<<EOF
                 _        _
                | |      | |
 _ __  _ __ ___ | |_ ___ | |_ _   _ _ __   ___
| '_ \| '__/ _ \| __/ _ \| __| | | | '_ \ / _ \
| |_) | | | (_) | || (_) | |_| |_| | |_) |  __/
| .__/|_|  \___/ \__\___/ \__|\__, | .__/ \___|
| |                            __/ | |
|_|                           |___/|_|


EOF;

$versionFile = __DIR__ . '../VERSION';
$version = file_exists($versionFile) ? file_get_contents($versionFile) : 'dev';

$console = new Application($title, $version);
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

\LaFourchette\Console\Status::register($app, $console);
\LaFourchette\Console\Check::register($app, $console);
\LaFourchette\Console\Create::register($app, $console);
\LaFourchette\Console\Delete::register($app, $console);
\LaFourchette\Console\Reset::register($app, $console);
\LaFourchette\Console\Stop::register($app, $console);
\LaFourchette\Console\Start::register($app, $console);
\LaFourchette\Console\GetVmId::register($app, $console);

return $console;
