<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Expired extends NotifyAbstract
{
    /**
     * @param Vm $vm
     * @return string
     */
    public function getSubject(Vm $vm)
    {
        $integ = $vm->getInteg();
        $name = $integ->getName();

        return sprintf('Votre environnement de test %s a expiré', $name);
    }

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

L'environnement de test {$name} a expiré et va être supprimé d'ici quelques instants.

En espérant que la recette du/des projets a été fructueuse.

Cordialement
EOS;

        return $str;
    }
}
