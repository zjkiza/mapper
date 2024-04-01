<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\TransformerMetadata;

final class TransformerStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Transformer);

        $builder->setTransformerMetadata(
            TransformerMetadata::create($attributeInstance->name)
        );
    }

    public function kay(): string
    {
        return Transformer::class;
    }
}
