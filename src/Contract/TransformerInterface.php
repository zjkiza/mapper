<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Metadata\Property;

interface TransformerInterface
{
    /**
     * Transforms value of entity property to a DTO property.
     */
    public function transform(object $entity, Property $property): mixed;

    /**
     * Transforms value of DTO property to a entity property.
     */
    public function reverse(object $dto, Property $property): mixed;
}
