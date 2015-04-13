<?php

namespace LaFourchette\Twig\Extensions;

use LaFourchette\Entity\User;
use LaFourchette\Entity\Vm;
use LaFourchette\Logger\VmLogger;
use \GeSHi;

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
            'la_fourchette_prototype_vm_status' => new \Twig_Function_Method(
                $this,
                'vmStatus',
                array('is_safe' => array(true))
            ),
            'la_fourchette_prototype_show_log' => new \Twig_Function_Method(
                $this,
                'showLog',
                array('is_safe' => array(true))
            ),
            'la_fourchette_prototype_integ_availability' => new \Twig_Function_Method(
                $this,
                'integAvailability'
            ),
            'vm_username' => new \Twig_Function_Method(
                    $this,
                    'vmUsername',
                    array('is_safe' => array(true))
                ),
        );
    }

    public function vmUsername(Vm $vm)
    {
        $created = $vm->getCreatedBy();
        return $created instanceof User ? $created->getUsername() : 'unknown';
    }

    public function showLog(Vm $vm)
    {
        $logFile = VmLogger::getLogFile($vm->getIdVm());

        if (!file_exists($logFile)) {
            return null;
        }

        $geshi = new \GeSHi(file_get_contents($logFile), 'bash');

        return $geshi->parse_code();
    }

    /**
     *
     * @param int $status
     */
    public function vmStatus($status)
    {
        switch ($status) {
            case Vm::RUNNING:
                return '<label class="label label-info">Running</label>';
                break;
            case Vm::STOPPED:
                return '<label class="label label-danger">Stopped</label>';
                break;
            case Vm::SUSPEND:
                return '<label class="label label-warning">Suspend</label>';
                break;
            case Vm::EXPIRED:
                return '<label class="label label-default">Expired</label>';
                break;
            case Vm::TO_START:
                return '<label class="label label-info">To start</label>';
                break;
            case Vm::STARTED:
                return '<label class="label label-info">Starting</label>';
                break;
            case Vm::ARCHIVED:
                return '<label class="label label-info">Archived</label>';
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
