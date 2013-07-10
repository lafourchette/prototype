<?php

namespace LaFourchette\Console;

use LaFourchette\Entity\Vm;
use Symfony\Component\Console\Application;
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
            ->setDescription('Check all state of VM')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
                $command = new Check();
                $command->setApplication($app);
                $command->run($input, $output);
            });
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $vmManager = $this->getVmManager();
        /**
         * @var Vm[] $vms
         */
        $vms = $vmManager->loadAll();

        $provisioner = $this->getProvisioner();

        $notify = $this->getNotify();

        foreach ($vms as $vm) {

            $savedStatus = $vm->getStatus();
            $currentStatus = $provisioner->getStatus($vm);

            if ($currentStatus == Vm::TO_START) {
                $provisioner->start($vm);

                if ($vm->getStatus() == Vm::RUNNING) {
                    $notify->send('ready', $vm);
                } else {
                    $notify->send('unable_to_start', $vm);
                }
            } else {
                if ($savedStatus != $currentStatus) {
                    $vm->setStatus($currentStatus);
                    $vmManager->save($vm);

                    switch ($currentStatus){
                        case Vm::RUNNING:
                            //Nothing to do
                            break;
                        case Vm::STOPPED:
                            if ($savedStatus != Vm::STOPPED) {
                                //Someone else have killed the VM (serveur ? admin ? other ?) Something wrong append
                                $notify->send('killed', $vm);
                            }
                            break;
                        case Vm::SUSPEND:
                            //todo: this case is currently not used
                            break;
                        case Vm::MISSING:

                            break;
                        case Vm::EXPIRED:
                            if ($savedStatus != Vm::EXPIRED) {
                                $notify->send('expired', $vm);
                                $provisioner->stop($vm);
                            }
                            break;

                    }
                }
            }
        }
    }
}