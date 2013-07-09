<?php

namespace LaFourchette\Checker;

/**
 * Description of VmChecker
 *
 * @author gcavana
 */
class IntegAvailabibiltyChecker implements CheckerInterface
{

    protected $countInteg;
    protected $countVm;

    public function __construct($countInteg, $countVm)
    {
        $this->countInteg = $countInteg;
        $this->countVm = $countVm;
    }

    public function check()
    {
        return !($this->countVm >= $this->countInteg);
    }

    public function getName()
    {
        return 'integ_availabibilty';
    }

}