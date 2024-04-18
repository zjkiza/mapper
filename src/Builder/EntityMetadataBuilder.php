<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder;

use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\EntityMetadata;

final class EntityMetadataBuilder
{
    private string $entity;

    private bool $newEntity = false;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function setEntity(string $entity): EntityMetadataBuilder
    {
        $this->entity = $entity;

        return $this;
    }

    public function setNewEntity(bool $newEntity): EntityMetadataBuilder
    {
        $this->newEntity = $newEntity;

        return $this;
    }

    public function build(string $dtoClass): EntityMetadata
    {
        NotExistAttribute::throwIf(
            !isset($this->entity),
            \sprintf('Entity attribute is must be define on dto class "%s"', $dtoClass)
        );

        return EntityMetadata::create(
            $this->entity,
            $this->newEntity
        );
    }
}
