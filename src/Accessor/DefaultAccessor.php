<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor;

use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Contract\PropertyAccessInterface;
use Zjk\DtoMapper\Metadata\Property;

final readonly class DefaultAccessor implements DefaultAccessorInterface
{
    public function __construct(
        private MethodAccessorInterface $methodAccessor,
        private PropertyAccessInterface $propertyAccess
    ) {
    }

    public function callGetter(object $object, Property $property): mixed
    {
        return $this->methodAccessor->callGetter($object, $property);
    }

    public function callSetter(object $object, Property $property, mixed $value): void
    {
        $this->methodAccessor->callSetter($object, $property, $value);
    }

    public function getValue(object $object, Property $property): mixed
    {
        return $this->propertyAccess->getValue($object, $property);
    }

    public function setValue(object $object, Property $property, mixed $value): void
    {
        $this->propertyAccess->setValue($object, $property, $value);
    }
}
