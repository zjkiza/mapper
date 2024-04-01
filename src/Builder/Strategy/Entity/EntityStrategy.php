<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Entity;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Builder\EntityMetadataBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\EntityStrategyInterface;
use Zjk\DtoMapper\Exception\InvalidClassException;

final class EntityStrategy implements EntityStrategyInterface
{
    public function build(EntityMetadataBuilder $builder, AttributeInterface $attributeInstance): void
    {
        \assert($attributeInstance instanceof Entity);

        if (false === \class_exists($attributeInstance->name)) {
            throw new InvalidClassException(\sprintf('In Attribute Entity in the property name="%s", class with this name is not exist.', $attributeInstance->name));
        }

        $builder->setEntity($attributeInstance->name);
    }

    public function kay(): string
    {
        return Entity::class;
    }
}
