<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Entity;

use Zjk\DtoMapper\Attribute\NewEntity;
use Zjk\DtoMapper\Builder\EntityMetadataBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\EntityStrategyInterface;

final class NewEntityStrategy implements EntityStrategyInterface
{
    public function build(EntityMetadataBuilder $builder, AttributeInterface $attributeInstance): void
    {
        \assert($attributeInstance instanceof NewEntity);

        $builder->setNewEntity(true);
    }

    public function kay(): string
    {
        return NewEntity::class;
    }
}
