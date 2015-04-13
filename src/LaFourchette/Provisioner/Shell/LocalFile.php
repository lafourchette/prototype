<?php

namespace LaFourchette\Provisioner\Shell;

/**
 * Get a file on the system.
 */
class LocalFile
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }
}
