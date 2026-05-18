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
     * @param iterable<object> $collections
     *
     * @return \Generator<int, object, mixed, void>
     */
    public function fromCollectionEntityToDtoLazy(iterable $collections, object|string $target): \Generator;

    /**
     * @template T of object
     *
     * @param iterable<object>  $collections
     * @param T|class-string<T> $target
     *
     * @return array<int, T>
     */
    public function fromCollectionDtoToEntity(iterable $collections, object|string $target): array;

    /**
     * @template T of object
     *
     * @param iterable<object>  $collections
     * @param T|class-string<T> $target
     *
     * @return \Generator<int, T, mixed, void>
     */
    public function fromCollectionDtoToEntityLazy(iterable $collections, object|string $target): \Generator;

    /**
     * @param object|class-string $dto
     */
    public function fromObjectEntityToDto(object $entity, object|string $dto): object;

    /**
     * @template T of object
     *
     * @param T|class-string<T>          $entity
     * @param array<string, object>|null $entityLookupMap
     */
    public function fromObjectDtoToEntity(object $dto, object|string $entity, ?array $entityLookupMap = null): object;
}
