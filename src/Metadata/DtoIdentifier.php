<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

/**
 * DTO identifier store
 * ID value after transformer and all data for this property.
 */
final class DtoIdentifier
{
    public function __construct(
        protected string $entityClass,
        protected Property $property,
        protected int|string|null $value
    ) {
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getValue(): int|string|null
    {
        return $this->value;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public static function create(string $class, Property $property, int|string|null $value): self
    {
        return new self($class, $property, $value);
    }
}
