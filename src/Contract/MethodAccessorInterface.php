<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Metadata\Property;

interface MethodAccessorInterface
{
    public function callGetter(object $object, Property $property): mixed;

    public function callSetter(object $object, Property $property, mixed $value): void;
}
