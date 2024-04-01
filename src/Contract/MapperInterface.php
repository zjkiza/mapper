<?php

namespace Zjk\DtoMapper\Contract;

interface MapperInterface
{
    /**
     * @param iterable<object>            $collections
     * @param object|class-string<object> $target
     *
     * @return array<int, object>
     */
    public function fromCollectionEntityToDto(iterable $collections, object|string $target): array;

    /**
     * @param iterable<object>            $collections
     * @param object|class-string<object> $target
     *
     * @return array<int, object>
     */
    public function fromCollectionDtoToEntity(iterable $collections, object|string $target): array;

    /**
     * @param object|class-string<object> $dto
     */
    public function fromObjectEntityToDto(object $entity, object|string $dto): object;

    /**
     * @param object|class-string<object> $entity
     */
    public function fromObjectDtoToEntity(object $dto, object|string $entity): object;
}
