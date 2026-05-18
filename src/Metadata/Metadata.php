<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final readonly class Metadata
{
    /**
     * @param array<string, Property> $properties
     * @param class-string            $classNameDto
     */
    public function __construct(
        private EntityMetadata $entityMetadata,
        private string $classNameDto,
        private array $properties,
    ) {
    }

    public function getEntityMetadata(): EntityMetadata
    {
        return $this->entityMetadata;
    }

    /**
     * @return class-string
     */
    public function getClassNameDto(): string
    {
        return $this->classNameDto;
    }

    /**
     * @return array<string, Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array<string, Property> $property
     * @param class-string            $className
     */
    public static function create(EntityMetadata $entityMetadata, string $className, array $property): self
    {
        return new self($entityMetadata, $className, $property);
    }
}
