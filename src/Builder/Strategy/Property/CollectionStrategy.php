<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\RelationMetadata;

final class CollectionStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Collection);

        $builder->setLocalActionMetadata(
            RelationMetadata::create(Collection::class, $attributeInstance)
        );
    }

    public function kay(): string
    {
        return Collection::class;
    }
}
