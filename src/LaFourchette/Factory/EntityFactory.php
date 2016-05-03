<?php
namespace LaFourchette\Factory;

use LaFourchette\Entity\User;
use LaFourchette\Entity\Vm;

class EntityFactory
{
    private $entityTypes;

    public function __construct()
    {
        $this->entityTypes = [
            'user',
            'integ',
            'vm',
        ];
    }

    /**
     * @param $type
     *
     * @return User|Vm
     * @throws \InvalidArgumentException
     */
    public function getEntity($type)
    {
        if (!in_array($type, $this->entityTypes)) {
            throw new \InvalidArgumentException(sprintf('Type %s not supported', $type));
        }

        if ('vm' == $type) {
            return new Vm();
        }

        return new User();
    }
}
