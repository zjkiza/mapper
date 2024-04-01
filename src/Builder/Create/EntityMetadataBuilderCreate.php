<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder\Create;

use Zjk\DtoMapper\Builder\EntityMetadataBuilder;
use Zjk\DtoMapper\Builder\Strategy\Entity\EntityStrategy;
use Zjk\DtoMapper\Builder\Strategy\Entity\NewEntityStrategy;
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

    public function __construct()
    {
        $this
            ->addStrategy(new EntityStrategy())
            ->addStrategy(new NewEntityStrategy());
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
            \assert(\is_string($attributeName));

            // Make sure we only get ZJK Attributes
            if (!\is_subclass_of($attributeName, ClassAttributeInterface::class)) {
                continue;
            }

            /** @var AttributeInterface $attributeInstance */
            $attributeInstance = $attribute->newInstance();
            \assert($attributeInstance instanceof ClassAttributeInterface);

            $this->strategy($attributeInstance, $builder);
        }

        return $builder->build();
    }

    private function strategy(AttributeInterface $attributeInstance, EntityMetadataBuilder $builder): void
    {
        if (false === \array_key_exists($attributeInstance::class, $this->entityStrategy)) {
            return;
        }

        $this->entityStrategy[$attributeInstance::class]
            ->build($builder, $attributeInstance);
    }

    private function addStrategy(EntityStrategyInterface $entityStrategy): self
    {
        $this->entityStrategy[$entityStrategy->kay()] = $entityStrategy;

        return $this;
    }
}
