<?php

namespace LaFourchette\Location;

use Psr\Log\LoggerInterface;

/**
 * Remote server or local directory where a Prototype can be run.
 * It exposes methods allowing Provisioners to install necessary files,
 * run commands within the Prototype host or even on the Prototype.
 */
interface LocationInterface
{
    /**
     * @param LoggerInterface $logger Logger used by commands and file debugging.
     */
    function setLogger(LoggerInterface $logger);

    /**
     * Run a command on the Location.
     * @param string $cmd
     * @return array Output from the command
     */
    function runLocationCommand($cmd);

    /**
     * Run a command on the Prototype.
     * @param string $cmd
     * @return array Output from the command
     */
    function runPrototypeCommand($cmd);

    /**
     * Creates a file on the Location.
     * @param string $filename
     * @param string $content
     * @return bool True on success.
     */
    function createLocationFile($filename, $content);

    /**
     * Removes a file from the Location.
     * @param string $filename
     * @return bool True on success.
     */
    function removeLocationFile($filename);
}