<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Ready extends NotifyAbstract
{
    public function getContent(Vm $vm)
    {
        $integ = $vm->getInteg();
        $suffix = $integ->getSuffix();
        $name = $integ->getName();

        $expiredDt = $vm->getExpiredDt()->format('Y-m-d H:i:s');

        $str = <<<EOS
Bonjour,

Votre environnement de test {$name} est prêt.

Pour connaitre toutes les urls et commencer à s'en servir, rendez-vous sur cette page :
- http://status{$suffix}

Votre VM expirera automatiquement le {$expiredDt}.

Bonne recette
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

        return sprintf('Votre environnement de test %s est prêt', $name);
    }
}
