<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Create;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Zjk\DtoMapper\Builder\EntityMetadataBuilder;
use Zjk\DtoMapper\Contract\AttributeInterface;
use Zjk\DtoMapper\Contract\ClassAttributeInterface;
use Zjk\DtoMapper\Contract\EntityStrategyInterface;
use Zjk\DtoMapper\Metadata\EntityMetadata;

final class EntityMetadataBuilderCreate
{
    /**
     * @var array<class-string, EntityStrategyInterface>
     */
    private array $entityStrategy = [];

    /**
     * @param iterable<EntityStrategyInterface> $entityStrategies
     */
    public function __construct(
        #[AutowireIterator('zjk_dto_mapper.entity_strategy')]
        iterable $entityStrategies,
    ) {
        foreach ($entityStrategies as $strategy) {
            $this->entityStrategy[$strategy->key()] = $strategy;
        }
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     */
    public function create(\ReflectionClass $reflectionClass): EntityMetadata
    {
        $builder = EntityMetadataBuilder::create();

        foreach ($reflectionClass->getAttributes() as $attribute) {
            $attributeName = $attribute->getName();

            if (!\is_subclass_of($attributeName, ClassAttributeInterface::class)) {
                continue;
            }

            /** @var AttributeInterface $attributeInstance */
            $attributeInstance = $attribute->newInstance();
            \assert($attributeInstance instanceof ClassAttributeInterface);

            $this->strategy($attributeInstance, $builder);
        }

        return $builder->build($reflectionClass->getName());
    }

    private function strategy(AttributeInterface $attributeInstance, EntityMetadataBuilder $builder): void
    {
        if (!\array_key_exists($attributeInstance::class, $this->entityStrategy)) {
            return;
        }

        $this->entityStrategy[$attributeInstance::class]
            ->build($builder, $attributeInstance);
    }
}
