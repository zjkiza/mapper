<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

interface MapperInterface
{
    /**
     * @param iterable<object> $collections
     *
     * @return array<int, object>
     */
    public function fromCollectionEntityToDto(iterable $collections, object|string $target): array;

    /**
     * @param iterable<object>    $collections
     * @param object|class-string $target
     *
     * @return array<int, object>
     */
    public function fromCollectionDtoToEntity(iterable $collections, object|string $target): array;

    /**
     * @param object|class-string $dto
     */
    public function fromObjectEntityToDto(object $entity, object|string $dto): object;

    /**
     * @template T of object
     *
     * @param T|class-string $entity
     */
    public function fromObjectDtoToEntity(object $dto, object|string $entity): object;
}
