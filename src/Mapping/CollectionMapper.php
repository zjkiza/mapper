<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

final readonly class CollectionMapper
{
    public function __construct(
        private SingleEntityToDtoMapper $entityToDtoMapper,
        private SingleDtoToEntityMapper $dtoToEntityMapper,
        private EntityFactory $entityFactory,
        private IdentifierResolver $identifierResolver,
    ) {
    }

    /**
     * @param iterable<object> $collections
     *
     * @return \Generator<int, object, mixed, void>
     */
    public function entityToDtoLazy(iterable $collections, object|string $target): \Generator
    {
        foreach ($collections as $source) {
            yield $this->entityToDtoMapper->map($source, $target);
        }
    }

    /**
     * @param iterable<object> $collections
     *
     * @return array<int, object>
     */
    public function entityToDto(iterable $collections, object|string $target): array
    {
        $dto = [];

        foreach ($collections as $source) {
            $dto[] = $this->entityToDtoMapper->map($source, $target);
        }

        return $dto;
    }

    /**
     * @template T of object
     *
     * @param iterable<int|string, object> $collections
     * @param T|class-string<T>            $target
     *
     * @return \Generator<int, T, mixed, void>
     */
    public function dtoToEntityLazy(iterable $collections, object|string $target): \Generator
    {
        $entityLookupMap = $this->buildLookupMap($collections);

        foreach ($collections as $source) {
            /** @var T $entity */
            $entity = $this->dtoToEntityMapper->map($source, $target, $entityLookupMap);
            yield $entity;
        }
    }

    /**
     * @template T of object
     *
     * @param iterable<int|string, object> $collections
     * @param T|class-string<T>            $target
     *
     * @return array<int, T>
     */
    public function dtoToEntity(iterable $collections, object|string $target): array
    {
        $entityLookupMap = $this->buildLookupMap($collections);

        $entities = [];

        foreach ($collections as $source) {
            $entities[] = $this->dtoToEntityMapper->map($source, $target, $entityLookupMap);
        }

        /**
         * @template T of object
         *
         * @var array<int, T> $entities
         */
        return $entities;
    }

    /**
     * @param iterable<int|string,object> $collections
     *
     * @return array<string, object>
     */
    private function buildLookupMap(iterable $collections): array
    {
        /** @var list<int|string> $identifiers */
        $identifiers = [];
        /** @var array<int, DtoToEntityBatchItem<object>> $dtoWithIdentifier */
        $dtoWithIdentifier = [];

        foreach ($collections as $index => $source) {
            $dtoIdentifier = $this->identifierResolver->resolve($source);

            $value = $dtoIdentifier->getValue();
            if (null === $value) {
                continue;
            }

            \assert(\is_int($value) || \is_string($value));

            $identifiers[] = $value;

            /** @var class-string $entityClass */
            $entityClass = $dtoIdentifier->getEntityClass();

            $dtoWithIdentifier[$index] = new DtoToEntityBatchItem(
                $source,
                $dtoIdentifier,
                $entityClass,
            );
        }

        return $this->entityFactory->buildLookupMap($identifiers, $dtoWithIdentifier);
    }
}
