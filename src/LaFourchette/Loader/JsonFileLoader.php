<?php
namespace LaFourchette\Loader;

class JsonFileLoader extends FileLoader
{
    public function load()
    {
        foreach ($this->files as $fileType => $fileName) {
            $jsonData = json_decode(file_get_contents($fileName), true);
            if (is_array($jsonData)) {
                foreach ($jsonData as $data) {
                    $this->data[$fileType][] = $this->getDenormalizedEntity($fileType, $data);
                }
            }
        }
    }
}