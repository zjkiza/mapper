<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Create;

use Zjk\DtoMapper\Builder\PropertyBuilder;
use Zjk\DtoMapper\Builder\Strategy\Property\CollectionStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\DtoStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\GetterStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\IdentifierStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\RepositoryStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\SetterStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\TransformerStrategy;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\PropertyAttributeInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Settings\Settings;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class PropertyBuilderCreate
{
    /**
     * @var array<class-string,PropertyStrategyInterface>
     */
    private array $propertyStrategy = [];

    public function __construct()
    {
        $this
            ->addStrategy(new CollectionStrategy())
            ->addStrategy(new DtoStrategy())
            ->addStrategy(new GetterStrategy())
            ->addStrategy(new IdentifierStrategy())
            ->addStrategy(new RepositoryStrategy())
            ->addStrategy(new SetterStrategy())
            ->addStrategy(new TransformerStrategy());
    }

    public function create(\ReflectionProperty $property, EntityMetadata $entityMetadata): Property
    {
        $propertyBuilder = PropertyBuilder::creat();
        $propertyBuilder->setName($property->getName());
        $propertyBuilder->setGetter(
            Settings::getter($property->getName())
        );
        $propertyBuilder->setSetter(
            Settings::setter($property->getName())
        );

        foreach ($property->getAttributes() as $attribute) {
            $attributeName = $attribute->getName();

            // Make sure we only get ZJK Attributes
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
        if (false === \array_key_exists($attributeInstance::class, $this->propertyStrategy)) {
            return;
        }

        $this->propertyStrategy[$attributeInstance::class]
            ->build($propertyBuilder, $attributeInstance, $entityMetadata);
    }

    private function addStrategy(PropertyStrategyInterface $propertyStrategy): self
    {
        $this->propertyStrategy[$propertyStrategy->kay()] = $propertyStrategy;

        return $this;
    }
}
