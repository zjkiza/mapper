<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Property;

use Doctrine\Persistence\Proxy;
use Zjk\DtoMapper\Contract\PropertyAccessInterface;
use Zjk\DtoMapper\Metadata\Property;

final readonly class DoctrineProxyPropertyAccessor implements PropertyAccessInterface
{
    public function __construct(
        private PrivatePropertyAccess $propertyAccess
    ) {
    }

    public function getValue(object $object, Property $property): mixed
    {
        if (\interface_exists(Proxy::class) && $object instanceof Proxy && !$object->__isInitialized()) {
            $object->__load();
        }

        return $this->propertyAccess->getValue($object, $property);
    }

    public function setValue(object $object, Property $property, mixed $value): void
    {
        if (\interface_exists(Proxy::class) && $object instanceof Proxy && !$object->__isInitialized()) {
            $object->__load();
        }

        $this->propertyAccess->setValue($object, $property, $value);
    }
}
