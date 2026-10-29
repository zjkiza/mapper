<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Zjk\DtoMapper\Contract\IdentifierInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\DtoIdentifier;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

/**
 * Builds a collection of entities from a collection of DTOs, existing entities are loaded in a single batch.
 */
final readonly class FullCollectionBuilder
{
    public function __construct(
        private EntityFactory $entityFactory,
        private IdentifierResolver $identifierResolver,
    ) {
    }

    /**
     * @template T of object
     *
     * @param iterable<T>                      $inputCollections
     * @param \Closure(object, object): object $mapper           maps the DTO (first argument) to the entity (second argument)
     *
     * @return array<string|int, T>
     *
     * @throws NotExistAttribute
     * @throws \ReflectionException
     */
    public function build(RepositoryMetadata $repositoryMetadata, iterable $inputCollections, \Closure $mapper): array
    {
        /** @var class-string $entityClass */
        $entityClass = $repositoryMetadata->getClassName();

        /**
         * @template T of object
         *
         * @var array<string|int, T> $newCollection
         */
        $newCollection = [];
        /**
         * @var array<int|string> $inputIdsFromRepository
         */
        $inputIdsFromRepository = [];
        /**
         * @var array<int|string, object> $dtoForEntity
         */
        $dtoForEntity = [];
        /**
         * @var array<int|string, DtoIdentifier> $dtoIdentifiers
         */
        $dtoIdentifiers = [];

        foreach ($inputCollections as $dto) {
            $dtoIdentifier = $this->identifierResolver->resolve($dto);

            $idValue = $dtoIdentifier->getValue();
            if (null === $idValue) {
                /** @var IdentifierInterface $entity */
                $entity = new $entityClass();

                $newCollection[$entity->getIdentifier()] = $mapper($dto, $entity);
                continue;
            }

            /** @var int|string $idValue */
            $inputIdsFromRepository[] = $idValue;
            $dtoForEntity[$idValue] = $dto;
            $dtoIdentifiers[$idValue] = $dtoIdentifier;
        }

        /**
         * @var IdentifierInterface[] $entityFromRepository
         */
        $entityFromRepository = $this->entityFactory->getRepository()->findAllByIdentifiers($inputIdsFromRepository, $entityClass);

        foreach ($this->findMissingIds($inputIdsFromRepository, $entityFromRepository) as $id) {
            $entity = $this->entityFactory->create($dtoIdentifiers[$id], $entityClass);
            $newCollection[$id] = $mapper($dtoForEntity[$id], $entity);
            unset($dtoForEntity[$id]);
        }

        foreach ($entityFromRepository as $entity) {
            $entityId = $entity->getIdentifier();
            $newCollection[$entityId] = $mapper($dtoForEntity[$entityId], $entity);
        }

        /**
         * @template T of object
         *
         * @var array<string|int, T> $newCollection
         */
        return $newCollection;
    }

    /**
     * Ids defined in DTOs for which the entity does not exist in the repository yet.
     *
     * @param array<int|string>             $inputIds
     * @param iterable<IdentifierInterface> $entityFromRepository
     *
     * @return array<int|string>
     */
    private function findMissingIds(array $inputIds, iterable $entityFromRepository): array
    {
        if (\iterator_count($entityFromRepository) === \count($inputIds)) {
            return [];
        }

        $existIds = \array_map(
            static fn (IdentifierInterface $identifier): int|string => $identifier->getIdentifier(),
            \iterator_to_array($entityFromRepository)
        );

        /** @var array<int|string> $missingIds */
        $missingIds = \array_diff($inputIds, $existIds);

        return $missingIds;
    }
}
