<?php
namespace LaFourchette\Writer;

use Symfony\Component\Serializer\Serializer;

/**
 * TODO: handle xml files
 *
 * @author Mickael
 */
class FileWriterFactory
{
    /** @var Serializer */
    private $serializer;

    /** @var array */
    private $serializerFormats;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
        $this->serializerFormats = [
            'json',
//            'xml',
        ];
    }

    public function getWriter($serializerFormat)
    {
        if (!in_array($serializerFormat, $this->serializerFormats)) {
            throw new \InvalidArgumentException(sprintf('Serializer format %s not supported', $serializerFormat));
        }

        $className = '\LaFourchette\Writer\JsonFileWriter';
//        if ('xml' == $serializerFormat) {
//            $className = '\LaFourchette\Writer\XmlFileWriter';
//        }

        return new $className($this->serializer, $serializerFormat);
    }
}
