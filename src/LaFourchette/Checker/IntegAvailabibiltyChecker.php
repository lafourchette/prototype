<?php

namespace LaFourchette\Checker;

/**
 * Description of VmChecker
 *
 * @author gcavana
 */
class IntegAvailabibiltyChecker implements CheckerInterface
{

    protected $countA;
    protected $countB;

    public function __construct($countA, $countB)
    {
        $this->countA = $countA;
        $this->countB = $countB;
    }

    public function check()
    {
        return ($this->countA == $this->countB);
    }

    public function getName()
    {
        return 'integ_availabibilty';
    }

}