<?php
namespace LaFourchette\Loader;

class XmlFileLoader extends FileLoader
{
    public function load()
    {
        //TODO: finish implementation
//        foreach ($this->files as $fileType => $fileName) {
//            $xmlData = simplexml_load_string(file_get_contents($fileName));
//            if ($xmlData instanceof \SimpleXMLElement) {
//                foreach ($xmlData->children() as $child) {
//                    $this->data[$fileType][] = $this->getDenormalizedEntity($fileType, (array)$child);
//                }
//            }
//        }
    }
}
