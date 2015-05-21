<?php
namespace LaFourchette\Writer;

use Symfony\Component\Serializer\Serializer;

abstract class AbstractFileWriter
{
    /** @var Serializer */
    protected $serializer;

    /** @var string */
    protected $serializerFormat;

    public function __construct(Serializer $serializer, $serializerFormat)
    {
        $this->serializer = $serializer;
        $this->serializerFormat = $serializerFormat;
    }

    abstract public function write($fileName, $content);
}
