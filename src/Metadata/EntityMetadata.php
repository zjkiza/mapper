<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final readonly class EntityMetadata
{
    /**
     * @param class-string $className
     */
    public function __construct(private string $className, private bool $newEntity = false)
    {
    }

    /**
     * @return class-string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function isNewEntity(): bool
    {
        return $this->newEntity;
    }

    /**
     * @param class-string $entity
     */
    public static function create(string $entity, bool $newEntity = false): self
    {
        return new self($entity, $newEntity);
    }
}
