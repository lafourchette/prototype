<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Expired extends NotifyAbstract
{
    public function getContent(Vm $vm)
    {
        $integ = $vm->getInteg();
        $suffix = $integ->getSuffix();
        $name = $integ->getName();

        $expiredDt = $vm->getExpiredDt();

        $str = <<<EOS
Bonjour,

La VM {$name} a expiré et va être détruire d'ici quelques instants.

En espérant que la recette du/des projets a été fructueuse.

Cordialement
EOS;

        return $str;
    }
}