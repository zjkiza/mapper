<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Metadata\EntityMetadata;

interface PropertyStrategyInterface
{
    /**
     * Adding a builder to the PropertyBuilder depending on the defined attribute in the key.
     */
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void;

    /**
     * Returns the class of the attribute to which the strategy applies.
     *
     * @phpstan-return class-string
     */
    public function kay(): string;
}
