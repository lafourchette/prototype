<?php

namespace LaFourchette\Provisioner;

use LaFourchette\Manager\Vagrant;
use LaFourchette\Provisioner\Vagrant as BaseVagrant;
use LaFourchette\Provisioner\Dummy;

class ProvisionerFactory
{
    const PROVISIONER_VAGRANT = 'pvagrant';
    const PROVISIONER_DUMMY = 'pdummy';

    const MANAGER_VAGRANT = 'mvagrant';
    /**
     * @param $provisionerName
     * @param string $repo
     * @param string $defaultBranch
     * @return Vagrant|Dummy|Vagrant
     * @throws \Exception
     */
    public static function create($provisionerName, $repo = null, $defaultBranch = null)
    {
        if (trim($provisionerName) === '') {
            throw new \Exception('Type de provisioner manquant');
        }

        switch (trim($provisionerName)) {
            case self::PROVISIONER_VAGRANT:
                $provisioner = new BaseVagrant($repo, $defaultBranch);
                break;
            case self::PROVISIONER_DUMMY:
                $provisioner = new Dummy();
                break;
            case self::MANAGER_VAGRANT:
                $provisioner = new Vagrant($repo, $defaultBranch);
                break;
            default:
                throw new \Exception('Type de provisioner inconnu');
        }

        return $provisioner;
    }
}
