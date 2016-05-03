<?php
namespace LaFourchette\Loader;

use LaFourchette\Factory\EntityFactory;
use Symfony\Component\Serializer\Serializer;

/**
 * TODO: handle xml files
 *
 * @author Mickael
 */
class FileLoaderFactory
{
    /** @var Serializer */
    private $serializer;

    /** @var array */
    private $serializerFormats;

    private $configuration;

    /** @var EntityFactory */
    private $entityFactory;

    public function __construct(EntityFactory $entityFactory, Serializer $serializer, $configuration)
    {
        $this->entityFactory = $entityFactory;
        $this->serializer = $serializer;
        $this->configuration = $configuration;
        $this->serializerFormats = [
            'json',
//            'xml',
        ];
    }

    public function getLoader($serializerFormat)
    {
        if (!in_array($serializerFormat, $this->serializerFormats)) {
            throw new \InvalidArgumentException(sprintf('Serializer format %s not supported', $serializerFormat));
        }

        $className = '\LaFourchette\Loader\JsonFileLoader';
//        if ('xml' == $serializerFormat) {
//            $className = '\LaFourchette\Loader\XmlFileLoader';
//        }

        return new $className($this->entityFactory, $this->serializer, $serializerFormat, $this->configuration);
    }
}
