<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Builder\EntityMetadataBuilder;

interface EntityStrategyInterface
{
    /**
     * Adding a builder to the PropertyBuilder depending on the defined attribute in the key.
     */
    public function build(EntityMetadataBuilder $builder, AttributeInterface $attributeInstance): void;

    /**
     * Returns the class of the attribute to which the strategy applies.
     *
     * @return class-string
     */
    public function kay(): string;
}
