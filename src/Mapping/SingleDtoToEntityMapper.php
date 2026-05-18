<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\IdentifierInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\DtoIdentifier;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

use function Zjk\DtoMapper\checkIsAllValuesInArraySameType;

final readonly class SingleDtoToEntityMapper
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MetadataReaderInterface $metadataReader,
        private DefaultAccessorInterface $defaultAccessor,
        private TransformerInterface $transformer,
        private EntityFactory $entityFactory,
        private RelationResolver $relationResolver,
        private IdentifierResolver $identifierResolver,
    ) {
    }

    /**
     * @param class-string|object        $entity
     * @param array<string, object>|null $entityLookupMap
     */
    public function map(object $dto, object|string $entity, ?array $entityLookupMap = null): object
    {
        $metadata = $this->metadataReader->getMetadata($dto);

        $dtoIdentifier = $this->identifierResolver->resolve($dto);

        if (!\is_object($entity)) {
            $entity = ($metadata->getEntityMetadata()->isNewEntity())
                ? $this->entityFactory->create($dtoIdentifier, $entity)
                : $this->entityFactory->findOrCreate($dto, $entity, $metadata, $dtoIdentifier, $entityLookupMap);
        }

        foreach ($metadata->getProperties() as $property) {
            if (true === $property->isIdentifier()) {
                continue;
            }

            if (true === $property->hasTransformerMetadata()) {
                $value = $this->transformer->reverse($dto, $property);
                $this->defaultAccessor->callSetter($entity, $property, $value);
                continue;
            }

            if (true === $property->hasRepositoryMetadata()) {
                $this->resolveRepositoryRelation($entity, $property, $dto);
                continue;
            }

            if (Dto::class === $property->getLocalActionMetadata()?->getStrategy()) {
                /** @var object $objectDto */
                $objectDto = $this->defaultAccessor->getValue($dto, $property);
                $entityFromDto = $this->entityFactory->getEntityFromRelationAttribute($property);

                $embeddedEntity = $this->map($objectDto, $entityFromDto);
                $this->defaultAccessor->callSetter($entity, $property, $embeddedEntity);
                continue;
            }

            $value = $this->defaultAccessor->getValue($dto, $property);
            $this->defaultAccessor->callSetter($entity, $property, $value);
        }

        $this->entityManager->persist($entity);

        return $entity;
    }

    /**
     * @param class-string $entityClassName
     */
    public function createEntity(DtoIdentifier $dtoIdentifier, string $entityClassName): object
    {
        return $this->entityFactory->create($dtoIdentifier, $entityClassName);
    }

    /**
     * @throws NotExistAttribute
     */
    private function resolveRepositoryRelation(object $entity, Property $property, object $dto): void
    {
        $localAction = $property->getLocalActionMetadata();
        if (!$localAction instanceof \Zjk\DtoMapper\Metadata\RelationMetadata) {
            throw new NotExistAttribute('Attribute Collection is must by define when used attribute Repository.');
        }

        /** @var RepositoryMetadata $repositoryMetadata */
        $repositoryMetadata = $property->getRepositoryMetadata();

        /** @var array<array{}|object> $inputCollections */
        $inputCollections = $this->defaultAccessor->getValue($dto, $property);

        if (false === (bool) $inputCollections) {
            $this->defaultAccessor->setValue($entity, $property, new ArrayCollection());

            return;
        }

        checkIsAllValuesInArraySameType($inputCollections, 'object');

        /** @var array<object> $inputCollections */
        $newCollection = $this->resolveNewCollection($repositoryMetadata, $inputCollections, $dto);

        if (false === (bool) $newCollection) {
            $this->defaultAccessor->setValue($entity, $property, new ArrayCollection());

            return;
        }

        $previousCollection = $this->getPreviousCollection($entity, $property);

        $this->relationResolver->syncCollection($entity, $repositoryMetadata, $newCollection, $previousCollection);
    }

    /**
     * @param object[] $inputCollections
     *
     * @return array<int|string, object>
     */
    private function resolveNewCollection(RepositoryMetadata $repositoryMetadata, array $inputCollections, object $dto): array
    {
        if ($repositoryMetadata->isOnlyRelation()) {
            /** @var array<int|string, object> $newCollection */
            $newCollection = $this->relationResolver->buildRelationCollection($repositoryMetadata, $inputCollections, $dto);

            return $newCollection;
        }

        /** @var array<int|string, object> $newCollection */
        $newCollection = $this->buildFullCollection($repositoryMetadata, $inputCollections);

        return $newCollection;
    }

    /**
     * @template T of object
     *
     * @param iterable<T> $inputCollections
     *
     * @return array<string|int, T>
     *
     * @throws NotExistAttribute
     * @throws \ReflectionException
     */
    private function buildFullCollection(RepositoryMetadata $repositoryMetadata, iterable $inputCollections): array
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

                $newCollection[$entity->getIdentifier()] = $this->map($dto, $entity);
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

        if (\iterator_count($entityFromRepository) !== \count($inputIdsFromRepository)) {
            $existIdRepository = \array_map(
                static fn (IdentifierInterface $identifier): int|string => $identifier->getIdentifier(),
                \iterator_to_array($entityFromRepository)
            );

            /** @var array<int|string> $addNewEntityWithDefinedId */
            $addNewEntityWithDefinedId = \array_diff($inputIdsFromRepository, $existIdRepository);

            foreach ($addNewEntityWithDefinedId as $id) {
                $entity = $this->createEntity($dtoIdentifiers[$id], $entityClass);
                $newCollection[$id] = $this->map($dtoForEntity[$id], $entity);
                unset($dtoForEntity[$id]);
            }
        }

        foreach ($entityFromRepository as $entity) {
            $entityId = $entity->getIdentifier();
            $newCollection[$entityId] = $this->map($dtoForEntity[$entityId], $entity);
        }

        /**
         * @template T of object
         *
         * @var array<string|int, T> $newCollection
         */
        return $newCollection;
    }

    /**
     * @return array<int|string, object>
     */
    private function getPreviousCollection(object $entity, Property $property): array
    {
        /**
         * @psalm-var ArrayCollection<int, object> $arrayCollection
         */
        $arrayCollection = $this->defaultAccessor->callGetter($entity, $property);

        /** @var IdentifierInterface[] $previousArrayFromDatabase */
        $previousArrayFromDatabase = $arrayCollection->toArray();

        if (false === (bool) $previousArrayFromDatabase) {
            return [];
        }

        $previousCollectionIds = \array_map(
            static fn (IdentifierInterface $entity): int|string => $entity->getIdentifier(),
            $previousArrayFromDatabase
        );

        return \array_combine($previousCollectionIds, $previousArrayFromDatabase);
    }
}
