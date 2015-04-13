<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Killed extends NotifyAbstract
{
    /**
     * @param Vm $vm
     * @return string
     */
    public function getContent(Vm $vm)
    {
        $integ = $vm->getInteg();
        $suffix = $integ->getSuffix();
        $name = $integ->getName();

        $expiredDt = $vm->getExpiredDt();

        $str = <<<EOS
Bonjour,

L'environnement {$name} a cessé de fonctionner inopinément.
Il s'agit d'un cas exceptionnel qui ne devrait pas se reproduire.

Si cela resurvient, merci de contacter l'administrateur

Cordialement
EOS;

        return $str;
    }

    /**
     * @param Vm $vm
     * @return string
     */
    public function getSubject(Vm $vm)
    {
        $integ = $vm->getInteg();
        $name = $integ->getName();

        return sprintf('Une erreur est survenue sur l\'environnement %s.', $name);
    }
}
