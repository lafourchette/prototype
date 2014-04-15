<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class ExpireSoon extends NotifyAbstract
{
    /**
     * @param Vm $vm
     * @return string
     */
    public function getSubject(Vm $vm) {
        $integ = $vm->getInteg();
        $name = $integ->getName();

        return sprintf('Votre environnement de test %s va expirer dans 1h', $name);
    }

    /**
     * @param Vm $vm
     * @return string
     */
    public function getContent(Vm $vm)
    {
        $integ = $vm->getInteg();
        $name = $integ->getName();

        $str = <<<EOS
Bonjour,

L'environnement de test {$name} va expirer dans 1h et par conséquent va être supprimé.

Si vous en avez encore l'utilité veuillez rajouter un nouveau délais dessus.

Cordialement
EOS;

        return $str;
    }
}