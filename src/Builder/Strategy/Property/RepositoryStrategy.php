<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Strategy\Property;

use Zjk\DtoMapper\Attribute\RepositoryClass;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Exception\InvalidClassException;
use Zjk\DtoMapper\Exception\InvalidMethodException;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

final class RepositoryStrategy implements PropertyStrategyInterface
{
    public function build(PropertyBuilder $builder, AttributeInterface $attributeInstance, EntityMetadata $entityMetadata): void
    {
        \assert($attributeInstance instanceof RepositoryClass);

        if (false === \class_exists($entityMetadata->getClassName())) {
            throw new InvalidClassException(\sprintf('In Attribute RepositoryClass in the property entityClassName="%s", class with this name is not exist.', $entityMetadata->getClassName()));
        }

        if (false === \method_exists($entityMetadata->getClassName(), $attributeInstance->addMethod)) {
            throw new InvalidMethodException(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Repository.', $attributeInstance->entityClassName, $attributeInstance->addMethod));
        }

        if (false === \method_exists($entityMetadata->getClassName(), $attributeInstance->removeMethod)) {
            throw new InvalidMethodException(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Repository.', $attributeInstance->entityClassName, $attributeInstance->removeMethod));
        }

        $builder->setRepositoryMetadata(
            RepositoryMetadata::create(
                className: $attributeInstance->entityClassName,
                addMethod: $attributeInstance->addMethod,
                removeMethod: $attributeInstance->removeMethod,
                onlyRelation: $attributeInstance->onlyRelation,
            )
        );
    }

    public function kay(): string
    {
        return RepositoryClass::class;
    }
}
