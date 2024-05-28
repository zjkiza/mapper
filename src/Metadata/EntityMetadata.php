<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final class EntityMetadata
{
    /**
     * @var class-string
     */
    protected string $className;

    private readonly bool $newEntity;

    /**
     * @param class-string $className
     */
    public function __construct(
        string $className,
        bool $newEntity = false
    ) {
        $this->className = $className;
        $this->newEntity = $newEntity;
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
