<?php

namespace LaFourchette\Logger;

class VmProcessLogFormatter
{
    public static function format($type, $data)
    {
        if ('err' === $type) {
            return $data."\n";
        } else {
            return $data."\n";
        }
    }
}
