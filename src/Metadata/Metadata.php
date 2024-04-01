<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final class Metadata
{
    protected ?string $entity = null;

    /**
     * @param array<string, Property> $properties
     */
    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected string $className,
        protected array $properties
    ) {
    }

    public function getEntityMetadata(): EntityMetadata
    {
        return $this->entityMetadata;
    }

    public function getClassName(): string
    {
        return $this->className;
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
     */
    public static function create(EntityMetadata $entityMetadata, string $className, array $property): self
    {
        return new self($entityMetadata, $className, $property);
    }
}
