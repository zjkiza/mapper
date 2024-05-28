<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\PropertyAttributeInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class RepositoryClass implements PropertyAttributeInterface
{
    public function __construct(
        /**
         * @phpstan-param class-string<object> $entityClassName
         */
        public string $entityClassName,
        public string $addMethod,
        public string $removeMethod,
        public bool $onlyRelation = false
    ) {
    }
}
