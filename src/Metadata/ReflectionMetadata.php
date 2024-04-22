<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Attribute\Ignore;
use Zjk\DtoMapper\Builder\Create\EntityMetadataBuilderCreate;
use Zjk\DtoMapper\Builder\Create\PropertyBuilderCreate;
use Zjk\DtoMapper\Contract\RelationAttributeInterface;
use Zjk\DtoMapper\Exception\RuntimeException;

final class ReflectionMetadata
{
    /** @var array<class-string, Metadata> */
    private array $dtosMetadata = [];

    /** @var array<class-string, bool> */
    private array $dtosMapped = [];

    public function __construct(
        private readonly PropertyBuilderCreate $propertyActionBuilder,
        private readonly EntityMetadataBuilderCreate $entityMetadataActionBuilder
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T $dto
     *
     * @return array<class-string, Metadata>
     *
     * @throws \ReflectionException
     */
    public function getDtosMetadata(object|string $dto): array
    {
        $dtoReflectionClass = new \ReflectionClass($dto);

        $this->createDtosMapped($dtoReflectionClass);

        foreach (\array_keys($this->dtosMapped) as $keyDto) {
            $dtoReflectionClass = new \ReflectionClass($keyDto);
            $this->dtosMetadata[$keyDto] = $this->getMetadata($dtoReflectionClass);
        }

        return $this->dtosMetadata;
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     */
    public function getMetadata(\ReflectionClass $reflectionClass): Metadata
    {
        /** @var Property[] $dtoProperty */
        $dtoProperty = [];

        $entityMetadata = $this->entityMetadataActionBuilder->create($reflectionClass);

        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if ($this->isIgnored($property)) {
                continue;
            }

            $propertyObject = $this->propertyActionBuilder->create($property, $entityMetadata);
            $dtoProperty[$propertyObject->getName()] = $propertyObject;
        }

        return Metadata::create($entityMetadata, $reflectionClass->getName(), $dtoProperty);
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     *
     * @throws \ReflectionException
     */
    private function createDtosMapped(\ReflectionClass $reflectionClass): void
    {
        $properties = $reflectionClass->getProperties();
        if (isset($this->dtosMapped[$reflectionClass->getName()])) {
            throw new RuntimeException(\sprintf('A circular structure was detected: %s -> %s', \implode(' -> ', \array_keys($this->dtosMapped)), $reflectionClass->getName()));
        }

        $this->dtosMapped[$reflectionClass->getName()] = true;

        foreach ($properties as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $attributeName = $attribute->getName();
                \assert(\is_string($attributeName));

                // Make sure we only get ZJK RelationAttributeInterface
                if (!\is_subclass_of($attributeName, RelationAttributeInterface::class)) {
                    continue;
                }

                /** @var Collection|Dto $attributeInstance */
                $attributeInstance = $attribute->newInstance();
                \assert($attributeInstance instanceof RelationAttributeInterface);

                $object = new \ReflectionClass($attributeInstance->getClassNameDto());
                $this->createDtosMapped($object);
            }
        }
    }

    protected function isIgnored(\ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes(Ignore::class);

        return (bool) $attributes;
    }
}
