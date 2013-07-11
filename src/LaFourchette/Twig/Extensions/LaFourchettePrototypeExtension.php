<?php

namespace LaFourchette\Twig\Extensions;

use LaFourchette\Entity\Vm;

/**
 * Description of PrototypeExtension
 *
 * @author gcavana
 */
class LaFourchettePrototypeExtension extends \Twig_Extension
{
    protected $integAvailabibiltyChecker;
    
    public function __construct(\LaFourchette\Checker\IntegAvailabibiltyChecker $integAvailabibiltyChecker)
    {
        $this->integAvailabibiltyChecker = $integAvailabibiltyChecker;
    }
    /**
     * 
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'la_fourchette_prototype_vm_status' =>  new \Twig_Function_Method($this, 'vmStatus',array('is_safe' => array(true))),
            'la_fourchette_prototype_integ_availability' => new \Twig_Function_Method($this, 'integAvailability')
            );
    }
    
    /**
     * 
     * @param int $status
     */
    public function vmStatus($status)
    {
        switch($status)
        {
            case Vm::RUNNING;
                return '<i class="label label-info">Running</i>';
                break;
            case Vm::STOPPED:
                return '<i class="label label-important">Stopped</i>';
                break;
            case Vm::SUSPEND:
                return '<i class="label label-warning">Suspend</i>';
                break;
            case Vm::EXPIRED:
                return '<i class="label">Expired</i>';
                break;
            case Vm::TO_START:
                return '<i class="label label-info">To start</i>';
                break;
            case Vm::STARTED:
                return '<i class="label label-info">Currently started</i>';
                break;
            case Vm::ARCHIVED:
                return '<i class="label label-info">Archived</i>';
                break;
            default:
                return 'no status available';
                break;
        }
        
    }
    
    public function integAvailability()
    {
        return $this->integAvailabibiltyChecker->check();
    }
    
    public function getName()
    {
        return 'la_fourchette_prototype_extension';
    }
}