<?php

namespace LaFourchette\Checker;

/**
 * Description of VmChecker
 *
 * @author gcavana
 */
class IntegAvailabibiltyChecker implements CheckerInterface
{

    protected $integManager;

    public function __construct($integManager)
    {
        $this->integManager = $integManager;
    }

    public function check()
    {
        $vmAvailable = $this->integManager->loadAllAvailable();

        if (empty($vmAvailable)) {
           return false;
        }

        return true;
    }

    public function getName()
    {
        return 'integ_availabibilty';
    }
}
