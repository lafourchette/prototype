<?php

namespace LaFourchette\Logger;

abstract class ProcessLogFormatterAbstract
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
