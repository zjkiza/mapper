<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Method;

use Zjk\DtoMapper\Accessor\Initializer\DoctrineProxyInitializer;
use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Metadata\Property;

final readonly class DoctrineProxyMethodAccessor implements MethodAccessorInterface
{
    public function __construct(
        private PrivateMethodAccess $methodAccess,
        private DoctrineProxyInitializer $proxyInitializer,
    ) {
    }

    #[\Override]
    public function callGetter(object $object, Property $property): mixed
    {
        $this->proxyInitializer->initializeIfProxy($object);

        return $this->methodAccess->callGetter($object, $property);
    }

    #[\Override]
    public function callSetter(object $object, Property $property, mixed $value): void
    {
        $this->proxyInitializer->initializeIfProxy($object);

        $this->methodAccess->callSetter($object, $property, $value);
    }
}
