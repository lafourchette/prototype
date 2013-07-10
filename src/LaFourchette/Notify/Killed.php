<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Killed extends NotifyAbstract
{
    public function getContent(Vm $vm)
    {
        $integ = $vm->getInteg();
        $suffix = $integ->getSuffix();
        $name = $integ->getName();

        $expiredDt = $vm->getExpiredDt();

        $str = <<<EOS
Bonjour,

La VM {$name} a cessé de fonctionner inopinément.
Il s'agit d'un cas exceptionnel qui ne devrait pas se reproduire.

Si cela resurvient, merci de contacter l'administrateur

Cordialement
EOS;

        return $str;
    }
}