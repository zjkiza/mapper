<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Metadata\Property;

interface PropertyAccessInterface
{
    public function getValue(object $object, Property $property): mixed;

    public function setValue(object $object, Property $property, mixed $value): void;
}
