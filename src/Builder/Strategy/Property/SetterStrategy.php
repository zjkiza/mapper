<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Setter;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Exception\InvalidMethodException;
use Zjk\DtoMapper\Metadata\EntityMetadata;

final class SetterStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Setter);

        if (false === \method_exists($entityMetadata->getClassName(), $attributeInstance->methodName)) {
            throw new InvalidMethodException(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Setter.', $entityMetadata->getClassName(), $attributeInstance->methodName));
        }

        $builder->setSetter($attributeInstance->methodName);
    }

    public function kay(): string
    {
        return Setter::class;
    }
}
