<?php

namespace LaFourchette\Loader;

use LaFourchette\Entity\Integ;
use LaFourchette\Entity\User;
use LaFourchette\Entity\UserNotify;
use LaFourchette\Entity\Vm;
use LaFourchette\Factory\EntityFactory;
use Symfony\Component\Serializer\Serializer;

abstract class FileLoader implements FileLoaderInterface
{
    const FILE_TYPE_USERS = 'user';
    const FILE_TYPE_USERS_NOTIFY = 'user_notify';
    const FILE_TYPE_VMS = 'vm';

    const FILE_USERS = 'users.json';
    const FILE_USERS_NOTIFY = 'users_notify.json';
    const FILE_VMS = 'vms.json';

    /** @var Integ[] */
    protected $integs;

    /** @var User[] */
    protected $users;

    /** @var UserNotify[] */
    protected $usersNotify;

    /** @var Vm[] */
    protected $vms;

    const RESOURCE_PATH = '/resources/';

    /** @var array */
    protected $data;

    /** @var array */
    protected $files;

    /** @var Serializer */
    protected $serializer;

    protected $serializerFormat;

    private $configuration;

    public function __construct(EntityFactory $entityFactory, Serializer $serializer, $serializerFormat, $configuration)
    {
        $this->entityFactory = $entityFactory;
        $this->serializer = $serializer;
        $this->serializerFormat = $serializerFormat;
        $this->configuration = $configuration;
        $this->data = [];

        $this->initFiles();
    }

    public function initFiles()
    {
        $rootPath = __DIR__ . '/../' . $this->configuration['path'];
        if (!is_dir($rootPath)) {
            mkdir($rootPath);
        }

        array_walk($this->configuration['files'], function(&$fileName) use ($rootPath) {
                $fileName = $rootPath . $fileName;
            });

        foreach ($this->configuration['files'] as $file) {
            if (!file_exists($file)) {
                touch($file);
            }
        }

        $this->files = $this->configuration['files'];
    }

    public function getFile($type)
    {
        if (array_key_exists($type, $this->files)) {
            return $this->files[$type];
        }

        return null;
    }

    /**
     * @return Integ[]
     */
    public function getIntegs()
    {
        return $this->integs;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return Vm[]
     */
    public function getVms()
    {
        return $this->vms;
    }

    /**
     * @return mixed
     */
    public function getData($type)
    {
        if (array_key_exists($type, $this->data)) {
            return $this->data[$type];
        }

        return [];
    }

    protected function getDenormalizedEntity($type, $data)
    {
        $entity = $this->entityFactory->getEntity($type);
        $entity->denormalize($this->serializer, $data, $this->serializerFormat);

        return $entity;
    }

}