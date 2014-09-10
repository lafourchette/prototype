<?php

namespace LaFourchette\Tests;

use Prophecy\Prophet;

abstract class ProphecyTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prophet
     */
    protected $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet;
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function createInstanceWithoutConstructor($className)
    {
        if (method_exists('ReflectionClass', 'newInstanceWithoutConstructor')) {
            $reflClass = new \ReflectionClass($className);

            return $reflClass->newInstanceWithoutConstructor();
        }

        $serialized = sprintf('O:%d:"%s":0:{}', strlen($className), $className);

        return unserialize($serialized);
    }

    public function setAttributeValue($object, $attribute, $value)
    {
        $refl = new \ReflectionProperty($object, $attribute);
        $refl->setAccessible(true);
        $refl->setValue($object, $value);
    }

    public function getProphecy($className)
    {
        return $this->prophet->prophesize($className);
    }
}