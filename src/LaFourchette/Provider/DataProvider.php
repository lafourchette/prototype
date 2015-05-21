<?php
namespace LaFourchette\Provider;

use LaFourchette\Entity\User;
use LaFourchette\Entity\Vm;
use LaFourchette\Loader\FileLoader;
use LaFourchette\Loader\FileLoaderFactory;
use LaFourchette\Manager\UserManager;
use Symfony\Component\Serializer\Serializer;

class DataProvider
{
    /** @var User[] */
    private $users;

    /** @var Vm[] */
    protected $vms;

    public function __construct(Serializer $serializer, FileLoaderFactory $loaderFactory, $serializerFormat)
    {
        $this->serializer = $serializer;
        $this->fileLoader = $loaderFactory->getLoader($serializerFormat);
        $this->loadData();
    }

    public function getFileName($object)
    {
        if ($object instanceof User || $object instanceof UserManager) {
            return $this->fileLoader->getFile(FileLoader::FILE_TYPE_USERS);
        } else {
            return $this->fileLoader->getFile(FileLoader::FILE_TYPE_VMS);
        }
    }

    public function getData($object)
    {
        if ($object instanceof User || $object instanceof UserManager) {
            return $this->users;
        } else {
            return $this->vms;
        }
        }

    public function loadData()
    {
        $this->fileLoader->load();

        $this->users = $this->fileLoader->getData(FileLoader::FILE_TYPE_USERS);
        $this->vms = $this->fileLoader->getData(FileLoader::FILE_TYPE_VMS);
    }

    /** @return User[] */
    public function getUsers()
    {
        return $this->users;
    }

    /** @return Vm[] */
    public function getVms()
    {
        return $this->vms;
    }
}
