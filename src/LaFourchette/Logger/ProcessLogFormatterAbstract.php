<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 11/04/2015
 * Time: 11:50
 */

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