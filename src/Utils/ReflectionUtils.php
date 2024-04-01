<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Utils;

use Zjk\DtoMapper\Exception\InvalidMethodException;
use Zjk\DtoMapper\Exception\InvalidPropertyException;

final class ReflectionUtils
{
    /**
     * @var array<string, \ReflectionProperty|\ReflectionMethod>
     */
    private static array $cache = [];

    public static function getAccessProperty(object $class, string $property): \ReflectionProperty
    {
        $reflectionClass = $class instanceof \ReflectionClass ? $class : new \ReflectionClass($class);
        $key             = \sprintf('property %s::%s', $reflectionClass->getName(), $property);

        if (isset(self::$cache[$key])) {
            /** @var \ReflectionProperty $reflectionProperty */
            $reflectionProperty = self::$cache[$key];

            return $reflectionProperty;
        }

        if ($reflectionClass->hasProperty($property) && !$reflectionClass->getProperty($property)->isStatic()) {
            $reflectionProperty = $reflectionClass->getProperty($property);
            $reflectionProperty->setAccessible(true);

            self::$cache[$key] = $reflectionProperty;

            return $reflectionProperty;
        }

        $parent = $reflectionClass->getParentClass();

        if (false !== $parent) {
            $reflectionProperty = self::getAccessProperty($parent, $property);

            self::$cache[$key] = $reflectionProperty;

            return $reflectionProperty;
        }

        throw new InvalidPropertyException(\sprintf('Property "%s" of the class "%s" does not exist.', $property, $reflectionClass->getName()));
    }

    public static function getAccessMethod(object $class, string $method): \ReflectionMethod
    {
        $reflectionClass = $class instanceof \ReflectionClass ? $class : new \ReflectionClass($class);
        $key             = \sprintf('method %s::%s', $reflectionClass->getName(), $method);

        if (isset(self::$cache[$key])) {
            /** @var \ReflectionMethod $reflectionMethod */
            $reflectionMethod = self::$cache[$key];

            return $reflectionMethod;
        }

        if ($reflectionClass->hasMethod($method) && !$reflectionClass->getMethod($method)->isStatic()) {
            $reflectionMethod = $reflectionClass->getMethod($method);
            $reflectionMethod->setAccessible(true);

            self::$cache[$key] = $reflectionMethod;

            return $reflectionMethod;
        }

        $parent = $reflectionClass->getParentClass();

        if (false !== $parent) {
            $reflectionMethod = self::getAccessMethod($parent, $method);

            self::$cache[$key] = $reflectionMethod;

            return $reflectionMethod;
        }

        throw new InvalidMethodException(\sprintf('Method "%s" of the class "%s" does not exist.', $method, $reflectionClass->getName()));
    }
}
