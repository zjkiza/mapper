<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;

final class IdentifierStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        $builder->setIdentifier(true);
    }

    public function kay(): string
    {
        return Identifier::class;
    }
}
