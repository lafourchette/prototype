<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class UnableToStart extends NotifyAbstract
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

L'environnement {$name} n'a pu démarrer correctement

Merci de contacter l'administrateur pour que le problème soit résolu.

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

        return sprintf('Impossible de démarrer l\'environnement %s.', $name);
    }
}