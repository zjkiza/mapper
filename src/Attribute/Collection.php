<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\PropertyAttributeInterface;
use Zjk\DtoMapper\Contract\RelationAttributeInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Collection implements PropertyAttributeInterface, RelationAttributeInterface
{
    /**
     * @phpstan-param  class-string $className
     */
    public function __construct(
        /** @phpstan-var  class-string  */
        public string $className
    ) {
    }
}
