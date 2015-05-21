<?php

namespace LaFourchette\Manager;

use LaFourchette\Entity\Vm;

/**
 * Description of VmManager
 *
 * @author mdacosta
 */
class VmManager extends AbstractManager
{
    public function loadVm()
    {
        return $this->dataAccessService->getVmsWhereStatusNotIn(Vm::$freeStatus);
    }

    public function save($vm)
    {
        if (null === $vm->getIdVm()) {
            $vm->setIdVm($this->dataAccessService->getNextId('vm'));
        }

        parent::save($vm);
    }

    public function getActive()
    {
        return $this->getByStatus(Vm::RUNNING);
    }

    public function getToStart()
    {
        return $this->getByStatus(Vm::TO_START);
    }

    public function comment($id, $comment)
    {
        $vm = $this->load($id);
        $vm->setComment($comment);

        $this->save($vm);
    }

    private function getByStatus($status)
    {
        return count($this->dataAccessService->loadBy($this, ['status' => [$status]]));
    }
}
