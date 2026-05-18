<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

/**
 * DTO identifier store
 * ID value after transformer and all data for this property.
 */
final readonly class DtoIdentifier
{
    public function __construct(
        private string $dtoClass,
        private string $entityClass,
        private Property $property,
        private mixed $value,
    ) {
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getDtoClass(): string
    {
        return $this->dtoClass;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public static function create(string $classDto, string $classEntity, Property $property, mixed $value): self
    {
        return new self($classDto, $classEntity, $property, $value);
    }
}
