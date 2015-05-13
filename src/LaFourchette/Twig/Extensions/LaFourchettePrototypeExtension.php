<?php

namespace LaFourchette\Twig\Extensions;

use LaFourchette\Entity\User;
use LaFourchette\Entity\Vm;
use LaFourchette\Logger\VmLogger;
use \GeSHi;
use LaFourchette\Manager\IntegManager;

/**
 * Description of PrototypeExtension
 *
 * @author gcavana
 */
class LaFourchettePrototypeExtension extends \Twig_Extension
{
    /**
     * @var \LaFourchette\Manager\IntegManager
     */
    protected $integManager;

    public function __construct(IntegManager $integManager)
    {
        $this->integManager = $integManager;
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
                return '<div class="label flatblue pull-right m_x">Running</div>';
                break;
            case Vm::STOPPED:
                return '<div class="label label-danger">Stopped</div>';
                break;
            case Vm::SUSPEND:
                return '<div class="label label-warning">Suspend</div>';
                break;
            case Vm::EXPIRED:
                return '<div class="label label-default">Expired</div>';
                break;
            case Vm::TO_START:
                return '<div class="label label-info">To start</div>';
                break;
            case Vm::STARTED:
                return '
                <div class="label flatpurple pull-right m_x">
                    Building with love
                    <img src="img/bars.svg" class="mini-loader">
                </div>
                ';
                break;
            case Vm::ARCHIVED:
                return '<div class="label label-info">Archived</div>';
                break;
            default:
                return 'no status available';
                break;
        }
    }

    public function integAvailability()
    {
        return $this->integManager->hasAvailableInteg();
    }

    public function getName()
    {
        return 'la_fourchette_prototype_extension';
    }
}
