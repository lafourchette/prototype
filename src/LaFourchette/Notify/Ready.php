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

        $projectList = '';

        foreach($vm->getVmProjects() as $vmProject) {
            $projectList .= ' - ' . $vmProject->getProject()->getName() . ' : ' . $vmProject->getBranch() . "\n";
        }

        $expiredDt = $vm->getExpiredDt();

        $str = <<<EOS
Bonjour,

Votre environnement de test {$name} est prêt.

Pour connaitre toutes les urls et commencer à s'en servir, rendez-vous sur cette page :
- http://status{$suffix}

Voici un récapitulatif de ce qui a été installé :
{$projectList}

Votre VM expirera automatique le {$expiredDt}
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