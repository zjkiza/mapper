<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Property;

use Zjk\DtoMapper\Contract\PropertyAccessInterface;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Utils\ReflectionUtils;

final class PrivatePropertyAccess implements PropertyAccessInterface
{
    public function getValue(object $object, Property $property): mixed
    {
        $reflectionProperty = ReflectionUtils::getAccessProperty($object, $property->getName());

        return $reflectionProperty->isInitialized($object) ? $reflectionProperty->getValue($object) : null;
    }

    public function setValue(object $object, Property $property, mixed $value): void
    {
        $reflectionProperty = ReflectionUtils::getAccessProperty($object, $property->getName());

        $reflectionProperty->setValue($object, $value);
    }
}
