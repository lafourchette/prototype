<?php

namespace LaFourchette\Decider;

/**
 * Find the correct integ server to create a prototype
 *
 * @author gcavana
 */
class IntegDecider implements DeciderInterface
{
    public function decide()
    {
        ;
    }
    
    public function getName()
    {
        return 'integ_decider';
    }
}