<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Method;

use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Utils\ReflectionUtils;

final class PrivateMethodAccess implements MethodAccessorInterface
{
    public function callGetter(object $object, Property $property): mixed
    {
        $reflectionMethod = ReflectionUtils::getAccessMethod($object, $property->getGetter());

        return $reflectionMethod->invoke($object);
    }

    public function callSetter(object $object, Property $property, mixed $value): void
    {
        $reflectionMethod = ReflectionUtils::getAccessMethod($object, $property->getSetter());

        $reflectionMethod->invoke($object, $value);
    }
}
