<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Accessor\Initializer;

use Doctrine\Persistence\Proxy;

final readonly class DoctrineProxyInitializer
{
    public function initializeIfProxy(object $object): void
    {
        if (\interface_exists(Proxy::class) && $object instanceof Proxy && !$object->__isInitialized()) {
            $object->__load();
        }
    }
}
