<?php

declare(strict_types=1);

namespace Zjk\DtoMapper;

use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Mapping\CollectionMapper;
use Zjk\DtoMapper\Mapping\SingleDtoToEntityMapper;
use Zjk\DtoMapper\Mapping\SingleEntityToDtoMapper;

final readonly class Mapper implements MapperInterface
{
    public function __construct(
        private SingleEntityToDtoMapper $entityToDtoMapper,
        private SingleDtoToEntityMapper $dtoToEntityMapper,
        private CollectionMapper $collectionMapper,
    ) {
    }

    #[\Override]
    public function fromCollectionEntityToDto(iterable $collections, object|string $target): array
    {
        return $this->collectionMapper->entityToDto($collections, $target);
    }

    /**
     * @param iterable<object> $collections
     *
     * @return \Generator<int, object, mixed, void>
     */
    #[\Override]
    public function fromCollectionEntityToDtoLazy(iterable $collections, object|string $target): \Generator
    {
        return $this->collectionMapper->entityToDtoLazy($collections, $target);
    }

    /**
     * @template T of object
     *
     * @param iterable<int|string, object> $collections
     * @param T|class-string<T>            $target
     *
     * @return array<int, T>
     */
    #[\Override]
    public function fromCollectionDtoToEntity(iterable $collections, object|string $target): array
    {
        return $this->collectionMapper->dtoToEntity($collections, $target);
    }

    /**
     * @template T of object
     *
     * @param iterable<int|string, object> $collections
     * @param T|class-string<T>            $target
     *
     * @return \Generator<int, T, mixed, void>
     */
    #[\Override]
    public function fromCollectionDtoToEntityLazy(iterable $collections, object|string $target): \Generator
    {
        return $this->collectionMapper->dtoToEntityLazy($collections, $target);
    }

    #[\Override]
    public function fromObjectEntityToDto(object $entity, object|string $dto): object
    {
        return $this->entityToDtoMapper->map($entity, $dto);
    }

    /**
     * @template T of object
     *
     * @param T|class-string<T>          $entity
     * @param array<string, object>|null $entityLookupMap
     */
    #[\Override]
    public function fromObjectDtoToEntity(object $dto, object|string $entity, ?array $entityLookupMap = null): object
    {
        return $this->dtoToEntityMapper->map($dto, $entity, $entityLookupMap);
    }
}
