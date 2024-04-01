<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Exception\InvalidMethodException;
use Zjk\DtoMapper\Metadata\EntityMetadata;

final class GetterStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof Getter);

        if (false === \method_exists($entityMetadata->getClassName(), $attributeInstance->methodName)) {
            throw new InvalidMethodException(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Getter.', $entityMetadata->getClassName(), $attributeInstance->methodName));
        }

        $builder->setGetter($attributeInstance->methodName);
    }

    public function kay(): string
    {
        return Getter::class;
    }
}
