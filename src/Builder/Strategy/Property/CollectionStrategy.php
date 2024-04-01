<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Exception\InvalidClassException;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\LocalActionMetadata;
use Zjk\DtoMapper\Settings\Settings;

final class CollectionStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Collection);

        if (false === \class_exists($attributeInstance->className)) {
            throw new InvalidClassException(\sprintf('In Attribute Collection in the property className="%s", class with this name is not exist.', $attributeInstance->className));
        }

        $builder->setLocalActionMetadata(
            LocalActionMetadata::create(Settings::MAPPER_LOCAL_FUNCTION[$attributeInstance::class], $attributeInstance->className)
        );
    }

    public function kay(): string
    {
        return Collection::class;
    }
}
