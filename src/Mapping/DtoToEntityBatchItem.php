<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Zjk\DtoMapper\Metadata\DtoIdentifier;

/**
 * @template-covariant T of object
 */
final readonly class DtoToEntityBatchItem
{
    /**
     * @param T            $dto
     * @param class-string $entityClass
     */
    public function __construct(
        private object $dto,
        private DtoIdentifier $identifier,
        private string $entityClass,
    ) {
    }

    /**
     * @return T
     */
    public function getDto(): object
    {
        return $this->dto;
    }

    public function getIdentifier(): DtoIdentifier
    {
        return $this->identifier;
    }

    /**
     * @return class-string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
