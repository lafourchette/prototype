<?php

namespace LaFourchette\Decider;

use LaFourchette\Manager\IntegManager;

/**
 * Find the correct integ server to create a prototype
 *
 * @author gcavana
 */
class IntegDecider implements DeciderInterface
{
    protected $integManager;

    public function __construct(IntegManager $integManager)
    {
        $this->integManager = $integManager;
    }

    /**
     * Return an available integ
     * @return \LaFourchette\Entity\Integ
     */
    public function decide()
    {
        return $this->integManager->getBestInteg();
    }

    public function getName()
    {
        return 'integ_decider';
    }
}
