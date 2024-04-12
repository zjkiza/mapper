<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\LocalActionMetadata;
use Zjk\DtoMapper\Settings\Settings;

final class DtoStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Dto);

        $builder->setLocalActionMetadata(
            LocalActionMetadata::create(Settings::MAPPER_LOCAL_FUNCTION[$attributeInstance::class], $attributeInstance->className)
        );
    }

    public function kay(): string
    {
        return Dto::class;
    }
}
