<?php

namespace LaFourchette\Notify;

use \LaFourchette\Entity\Vm;

class Ready extends NotifyAbstract
{
    public function getContent(Vm $vm)
    {

        $integ = $vm->getInteg();
        $suffix = $integ->getSuffix();

        $projectList = '';

        foreach($vm->getVmProjects() as $vmProject) {
            $projectList .= ' - ' . $vmProject->getProject()->getName() . ' : ' . $vmProject->getBranch() . "\n";
        }

        $expiredDt = $vm->getExpiredDt();

        $str = <<<EOS
Bonjour,

Votre Vm est prête.

Pour connaitre toutes les urls, rendez-vous sur cette page :
- http://status

Voici un récapitulatif de ce qui a été installé :
{$projectList}

Votre VM expirera automatique le {$expiredDt}
Bonne recette

EOS;

        return $str;
    }
}