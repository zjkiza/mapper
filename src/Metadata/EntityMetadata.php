<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final class EntityMetadata
{
    public function __construct(
        protected string $className,
        private readonly bool $newEntity = false
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function isNewEntity(): bool
    {
        return $this->newEntity;
    }

    public static function create(string $entity, bool $newEntity = false): self
    {
        return new self($entity, $newEntity);
    }
}
