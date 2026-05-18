<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Create;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyAttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Settings\Settings;

final class PropertyBuilderCreate
{
    /**
     * @var array<class-string, PropertyStrategyInterface>
     */
    private array $propertyStrategy = [];

    /**
     * @param iterable<PropertyStrategyInterface> $propertyStrategies
     */
    public function __construct(
        #[AutowireIterator('zjk_dto_mapper.property_strategy')]
        iterable $propertyStrategies,
    ) {
        foreach ($propertyStrategies as $strategy) {
            $this->propertyStrategy[$strategy->key()] = $strategy;
        }
    }

    public function create(\ReflectionProperty $property, EntityMetadata $entityMetadata): Property
    {
        $propertyBuilder = PropertyBuilder::create();
        $propertyBuilder->setName($property->getName());
        $propertyBuilder->setGetter(
            Settings::getter($property->getName())
        );
        $propertyBuilder->setSetter(
            Settings::setter($property->getName())
        );

        foreach ($property->getAttributes() as $attribute) {
            $attributeName = $attribute->getName();

            if (!\is_subclass_of($attributeName, PropertyAttributeInterface::class)) {
                continue;
            }

            /** @var AttributeInterface $attributeInstance */
            $attributeInstance = $attribute->newInstance();
            \assert($attributeInstance instanceof PropertyAttributeInterface);

            $this->strategy($attributeInstance, $propertyBuilder, $entityMetadata);
        }

        return $propertyBuilder->build();
    }

    private function strategy(AttributeInterface $attributeInstance, PropertyBuilder $propertyBuilder, EntityMetadata $entityMetadata): void
    {
        if (!\array_key_exists($attributeInstance::class, $this->propertyStrategy)) {
            return;
        }

        $this->propertyStrategy[$attributeInstance::class]
            ->build($propertyBuilder, $attributeInstance, $entityMetadata);
    }
}
