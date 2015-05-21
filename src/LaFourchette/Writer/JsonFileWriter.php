<?php
namespace LaFourchette\Writer;

class JsonFileWriter extends AbstractFileWriter
{
    public function write($fileName, $content)
    {
        $fileObject = new \SplFileObject($fileName, 'w');
        if (null === $fileObject->fwrite($this->serializer->serialize($content, $this->serializerFormat))) {
            throw new \Exception(sprintf('Unable to save file %s', $fileName));
        }
    }
}
