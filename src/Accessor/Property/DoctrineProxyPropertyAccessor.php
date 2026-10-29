<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Property;

use Zjk\DtoMapper\Accessor\Initializer\DoctrineProxyInitializer;
use Zjk\DtoMapper\Contract\PropertyAccessInterface;
use Zjk\DtoMapper\Metadata\Property;

final readonly class DoctrineProxyPropertyAccessor implements PropertyAccessInterface
{
    public function __construct(
        private PrivatePropertyAccess $propertyAccess,
        private DoctrineProxyInitializer $proxyInitializer,
    ) {
    }

    #[\Override]
    public function getValue(object $object, Property $property): mixed
    {
        $this->proxyInitializer->initializeIfProxy($object);

        return $this->propertyAccess->getValue($object, $property);
    }

    #[\Override]
    public function setValue(object $object, Property $property, mixed $value): void
    {
        $this->proxyInitializer->initializeIfProxy($object);

        $this->propertyAccess->setValue($object, $property, $value);
    }
}
