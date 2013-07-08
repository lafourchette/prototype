<?php

namespace LaFourchette\Twig\Extensions;

/**
 * Description of PrototypeExtension
 *
 * @author gcavana
 */
class LaFourchettePrototypeExtension extends \Twig_Extension
{
    /**
     * 
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array('la_fourchette_prototype_vm_status' =>  new \Twig_Function_Method($this, 'vmStatus',array('is_safe' => array(true))));
    }
    
    /**
     * 
     * @param int $status
     */
    public function vmStatus($status)
    {
        switch($status)
        {
            case \LaFourchette\Entity\VM::RUNNING;
                return '<i class="label label-info">Running</i>';
                break;
            case \LaFourchette\Entity\VM::STOPPED:
                return '<i class="label label-important">Stopped</i>';
                break;
            case \LaFourchette\Entity\VM::SUSPEND:
                return '<i class="label label-warning">Suspend</i>';
                break;
            
            default:
                return 'no status available';
                break;
        }
        
    }
    
    public function getName()
    {
        return 'la_fourchette_prototype_extension';
    }
}