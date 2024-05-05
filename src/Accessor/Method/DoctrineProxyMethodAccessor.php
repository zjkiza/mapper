<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Method;

use Doctrine\Persistence\Proxy;
use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Metadata\Property;

final readonly class DoctrineProxyMethodAccessor implements MethodAccessorInterface
{
    public function __construct(private PrivateMethodAccess $methodAccess)
    {
    }

    public function callGetter(object $object, Property $property): mixed
    {
        if (\interface_exists(Proxy::class) && $object instanceof Proxy  && !$object->__isInitialized()) {
            $object->__load();
        }

        return $this->methodAccess->callGetter($object, $property);
    }

    public function callSetter(object $object, Property $property, mixed $value): void
    {
        if (\interface_exists(Proxy::class) && $object instanceof Proxy && !$object->__isInitialized()) {
            $object->__load();
        }

        $this->methodAccess->callSetter($object, $property, $value);
    }
}
