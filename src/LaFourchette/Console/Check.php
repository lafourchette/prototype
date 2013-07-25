<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use LaFourchette\Provisioner\Exception\UnableToStartException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Check extends ConsoleAbstract
{
    /**
     * @param \Silex\Application $app
     * @param Application $console
     */
    static public function register(\Silex\Application $app, Application $console)
    {
        $console->register('prototype:check')
            ->setDefinition(array(
                // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
            ))
            ->addArgument('vm-number', null, InputArgument::REQUIRED, 'The vm number')
            ->setDescription('Check all state of VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Check();
                $command->setApplication($app);
                $command->run($input, $output);
            });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $vmNumber = $input->getArgument('vm-number');

        $vmManager = $this->getVmManager();

        $vm = $vmManager->load($vmNumber);

        if ($vm !== null) {
            $vms = array($vm);
        } else {
            throw new \InvalidArgumentException('The given Vm could not be found');
        }

        $notify = $this->getNotify();

        $output->writeln('Start the checks of all VM');

        foreach ($vms as $vm) {
            $output->writeln('> VM ' . $vm->getName());
            $savedStatus = $vm->getStatus();
            $currentStatus = $this->application['vm.service']->getStatus($vm);

            $output->writeln('  - Old status: ' . $savedStatus);
            $output->writeln('  - Current status: ' . $currentStatus);

            if ($savedStatus == Vm::TO_START && $currentStatus != Vm::RUNNING) {
                $output->writeln('  - Need to be started');
                $output->writeln('  - Do it Now');
                $this->application['vm.service']->start($vm);
            } elseif ($savedStatus == Vm::ARCHIVED) {
                $output->writeln('  - Vm is archived.');
            } else {
                if ($savedStatus != $currentStatus) {
                    $vm->setStatus($currentStatus);
                    $vmManager->save($vm);

                    switch ($currentStatus){
                        case Vm::RUNNING:
                            $output->writeln('  - Running');
                            break;
                        case Vm::STOPPED:
                            if ($savedStatus != Vm::STOPPED && $savedStatus != Vm::EXPIRED && $savedStatus != Vm::ARCHIVED) {
                                $output->writeln('  - Has been just killed');
                                //Someone else have killed the VM (serveur ? admin ? other ?) Something wrong append
                                $notify->send('killed', $vm);
                            }
                            break;
                        case Vm::SUSPEND:
                            //todo: this case is currently not used
                            break;
                        case Vm::MISSING:
                                $output->writeln('  - Is missing');
                            break;
                        case Vm::EXPIRED:
                            if ($savedStatus != Vm::EXPIRED) {
                                $output->writeln('  - Has juste expired');
                                $notify->send('expired', $vm);
                                $this->application['vm.service']->stop($vm);
                            }
                            $vm->setStatus(Vm::ARCHIVED);
                            $this->application['vm.service']->delete($vm);
                            $this->application['vm.service']->prepare($vm);
                            break;
                    }
                }
            }
        }
    }
}