<?php

namespace LaFourchette\Provisioner;

class ProvisionerFactory
{
    const PROVISIONER_VAGRANT = 'vagrant';
    const PROVISIONER_DUMMY = 'dummy';

    /**
     * create Provisioner based on a given type
     *
     * @param string $type type of provisioner to create
     * @param mixed $provider data provider
     * @param array $configurations provisioner configurations
     *
     * @return Dummy|Vagrant
     * @throws \UnexpectedValueException
     */
    public static function create($type, $provider = null, $configurations = array())
    {
        switch ($type) {
            case self::PROVISIONER_VAGRANT:
                $provisioner = new Vagrant($provider, $configurations);
                break;
            case self::PROVISIONER_DUMMY:
                $provisioner = new Dummy($provider);
                break;
            default:
                throw new \UnexpectedValueException(sprintf('Undefined provisioner of type %s', $type));
        }

        return $provisioner;
    }
}
