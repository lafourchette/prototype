<?php

namespace LaFourchette\Model;

abstract class ModelAbstract
{
    /**
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        } else {
            throw new \Exception(sprintf('Unknown properties %s', $key));
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } else {
            throw new \Exception(sprintf('Unknown properties %s', $key));
        }
    }

    /**
     * @param string $method
     * @param mixed $arg
     * @throws \Exception
     */
    public function __call($method, $arg)
    {
        $methodName = substr($method, 0, 3);

        if ($methodName == 'set' || $methodName == 'get') {
            if (count($arg) > 1) {
                throw new \Exception('Too many argument');
            }

            $methodName = '__' . $methodName;
            $key = substr($method, 3);

            $this->$methodName($key, $arg[0]);
        }
    }
}