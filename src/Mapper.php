<?php

declare(strict_types=1);

namespace Zjk\DtoMapper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\IdentifierInterface;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Contract\RepositoryInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Exception\InvalidClassImplementationException;
use Zjk\DtoMapper\Exception\NotExistEntity;
use Zjk\DtoMapper\Exception\RuntimeException;
use Zjk\DtoMapper\Metadata\DtoIdentifier;
use Zjk\DtoMapper\Metadata\LocalActionMetadata;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

final class Mapper implements MapperInterface
{
    /**
     * @var array<class-string, Metadata>
     */
    private array $dtosMetadata = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RepositoryInterface $repository,
        private readonly ReflectionMetadata $reflectionMetadata,
        private readonly DefaultAccessorInterface $defaultAccessor,
        private readonly TransformerInterface $transformer
    ) {
    }

    /**
     * @template T of object
     *
     * @param T|class-string<T> $target
     *
     * @return array<int, T>
     */
    public function fromCollectionEntityToDto(iterable $collections, object|string $target): array
    {
        $dto = [];

        foreach ($collections as $source) {
            $dto[] = $this->fromObjectEntityToDto($source, $target);
        }

        return $dto;
    }

    /**
     * @template T of object
     *
     * @param T|class-string<T> $target
     *
     * @return array<int, T>
     */
    public function fromCollectionDtoToEntity(iterable $collections, object|string $target): array
    {
        $entities = [];

        foreach ($collections as $source) {
            $entities[] = $this->fromObjectDtoToEntity($source, $target);
        }

        /**
         * @template T of object
         *
         * @var array<int, T>  $entities
         */
        return $entities;
    }

    /**
     * @template T of object
     *
     * @param T|class-string<T> $dto
     *
     * @phpstan-return T
     */
    public function fromObjectEntityToDto(object $entity, object|string $dto): object
    {
        if (!\is_object($dto)) {
            $reflectionDto = new \ReflectionClass($dto);
            $dto = $reflectionDto->newInstance();
        }

        $metadata = $this->getMetadata($dto);

        foreach ($metadata->getProperties() as $property) {
            if (true === $property->hasTransformerMetadata()) {
                $value = $this->transformer->transform($entity, $property);
                $this->defaultAccessor->setValue($dto, $property, $value);
                continue;
            }

            if (null !== $property->getLocalActionMetadata()) {
                $value = $this->{$property->getLocalActionMetadata()->actionName()}(
                    $this->defaultAccessor->callGetter($entity, $property),
                    $property->getLocalActionMetadata()->getClassName()
                );

                $this->defaultAccessor->setValue($dto, $property, $value);
                continue;
            }

            $value = $this->defaultAccessor->callGetter($entity, $property);
            $this->defaultAccessor->setValue($dto, $property, $value);
        }

        return $dto;
    }

    /**
     * @template T of object
     *
     * @param object|class-string<T> $entity
     *
     * @retrun T
     */
    public function fromObjectDtoToEntity(object $dto, object|string $entity): object
    {
        $metadata = $this->getMetadata($dto);

        $dtoIdentifier = $this->getDtoIdentifier($dto, $metadata);

        if (!\is_object($entity)) {
            $entity = ($metadata->getEntityMetadata()->isNewEntity())
                ? $this->newEntity($dtoIdentifier, $entity)
                : $this->getEntity(
                    $dto,
                    $entity,
                    $metadata,
                    $dtoIdentifier,
                );
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
                $this->repository($entity, $property, $dto);
                continue;
            }

            $value = $this->defaultAccessor->getValue($dto, $property);
            $this->defaultAccessor->callSetter($entity, $property, $value);
        }

        $this->entityManager->persist($entity);

        return $entity;
    }

    private function repository(object $entity, Property $property, object $dto): void
    {
        /** @var RepositoryMetadata $repositoryMetadata */
        $repositoryMetadata = $property->getRepositoryMetadata();

        /** @var array<array{}|object> $inputCollections */
        $inputCollections = $this->defaultAccessor->getValue($dto, $property);

        if (false === (bool) $inputCollections) {
            $this->defaultAccessor->setValue($entity, $property, new ArrayCollection());

            return;
        }

        checkIsAllValuesInArraySameType($inputCollections, 'object');

        // It's not just a relation, but CRUD processing goes through the collection
        if (false === $repositoryMetadata->isOnlyRelation()) {
            // Must be defined if a collection is being processed
            if (!$property->getLocalActionMetadata() instanceof LocalActionMetadata) {
                throw new NotExistAttribute('Attribute Collection is must by define when used attribute Repository with property isOnlyRelation=false');
            }

            /** @var object[] $inputCollections */
            $newCollection = $this->newCollectionFromObject($repositoryMetadata, $inputCollections);
        } else {
            /** @var object[] $inputCollections */
            $newCollection = $this->newRelationCollection($repositoryMetadata, $inputCollections, $dto);
        }

        if (false === (bool) $newCollection) {
            $this->defaultAccessor->setValue($entity, $property, new ArrayCollection());

            return;
        }

        $previousCollection = $this->getPreviousCollection($entity, $property);

        $newKeys = \array_keys($newCollection);
        $previousKeyKeys = \array_keys($previousCollection);

        $addKey = \array_diff($newKeys, $previousKeyKeys);
        $removeKey = \array_diff($previousKeyKeys, $newKeys);
        $editKey = \array_intersect($newKeys, $previousKeyKeys);

        // Add new relation
        foreach ($addKey as $key) {
            $entity->{$repositoryMetadata->getAddMethod()}($newCollection[$key]);
        }

        // Remove
        foreach ($removeKey as $key) {
            $entity->{$repositoryMetadata->getRemoveMethod()}($previousCollection[$key]);
        }

        // Edit : Replace - remove old and add new
        foreach ($editKey as $key) {
            $entity->{$repositoryMetadata->getRemoveMethod()}($previousCollection[$key]);
            $entity->{$repositoryMetadata->getAddMethod()}($newCollection[$key]);
        }
    }

    /**
     * @template T of object
     *
     * @param iterable<T> $inputCollections
     *
     * @return array<string|int, T>
     *
     * @throws NotExistAttribute
     * @throws NotExistEntity
     * @throws \ReflectionException
     */
    private function newCollectionFromObject(RepositoryMetadata $repositoryMetadata, iterable $inputCollections): array
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
         * Ids entity for updating or creating.
         *
         * @var array<int|string> $inputIdsFromRepository
         */
        $inputIdsFromRepository = [];
        /**
         * DTO for updating or creating entity.
         *
         * @var array<int|string, object> $dtoForEntity
         */
        $dtoForEntity = [];
        /**
         * So that the data that has already been extracted would not have to be extracted again.
         *
         * @var array<int|string, DtoIdentifier> $dtoIdentifiers
         */
        $dtoIdentifiers = [];

        foreach ($inputCollections as $dto) {
            \assert(\is_object($dto));
            $metadata = $this->getMetadata($dto);
            $dtoIdentifier = $this->getDtoIdentifier($dto, $metadata);

            // Create new Entity
            if (null === $dtoIdentifier->getValue()) {
                /** @var IdentifierInterface $entity */
                $entity = new $entityClass();

                if (!($entity instanceof IdentifierInterface)) {
                    throw new InvalidClassImplementationException(\sprintf('Class "%s" is not implement "%s" interface. Did you forget to implement the interface?', $entityClass, IdentifierInterface::class));
                }

                $newCollection[$entity->getIdentifier()] = $this->fromObjectDtoToEntity($dto, $entity);
                continue;
            }

            $inputIdsFromRepository[] = $dtoIdentifier->getValue();
            $dtoForEntity[$dtoIdentifier->getValue()] = $dto;
            $dtoIdentifiers[$dtoIdentifier->getValue()] = $dtoIdentifier;
        }

        /**
         * Extract entities in one query instead single query in fromObjectDtoToEntity.
         *
         * @var IdentifierInterface[] $entityFromRepository
         */
        $entityFromRepository = $this->repository->findAllByIdentifiers($inputIdsFromRepository, $entityClass);

        if (\iterator_count($entityFromRepository) !== \count($inputIdsFromRepository)) {
            $existIdRepository = \array_map(static function (IdentifierInterface $identifier) {
                return $identifier->getIdentifier();
            }, \iterator_to_array($entityFromRepository));

            // difference between input and found => new entities to be created with input ID
            $addNewEntityWithDefinedId = \array_diff($inputIdsFromRepository, $existIdRepository);

            // Add Entity Through Dto
            foreach ($addNewEntityWithDefinedId as $id) {
                $entity = $this->newEntity($dtoIdentifiers[$id], $entityClass);
                $newCollection[$id] = $this->fromObjectDtoToEntity($dtoForEntity[$id], $entity);
                unset($dtoForEntity[$id]);
            }
        }

        // Updating Entity Through Dto
        foreach ($entityFromRepository as $entity) {
            $newCollection[$entity->getIdentifier()] = $this->fromObjectDtoToEntity($dtoForEntity[$entity->getIdentifier()], $entity);
        }

        /**
         * @template T of object
         *
         * @var array<string|int, T> $newCollection
         */
        return $newCollection;
    }

    /**
     * @template T of object
     *
     * @param iterable<T> $inputCollections
     *
     * @return array<string|int, T>
     */
    private function newRelationCollection(RepositoryMetadata $repositoryMetadata, iterable $inputCollections, object $dto): array
    {
        /** @var class-string $class */
        $class = $repositoryMetadata->getClassName();

        $inputArray = iterable_to_array($inputCollections);

        checkIsAllValuesInArraySameType($inputArray, 'object');

        /** @var object|false $first */
        $first = \reset($inputArray);

        if (false === $first) {
            return [];
        }

        /** @var Metadata $metadata */
        $metadata = $this->getMetadata($first);

        $ids = \array_filter(
            \array_map(fn (object $dto): int|string|null => $this->getDtoIdentifier($dto, $metadata)->getValue(), $inputArray),
            static fn (int|string|null $value): bool => null !== $value
        );

        $collection = $this->repository->findAllByIdentifiers($ids, $class);

        /** @var T[] $arrayFromCollection */
        $arrayFromCollection = \iterator_to_array($collection);

        if (\count($ids) !== \count($arrayFromCollection)) {
            throw new RuntimeException(\sprintf('There are duplicate entities with ids "%s" in collection "%s". In DTO "%s", you must insert a validator to check for duplicates in the input collection.', \implode(', ', \array_diff_assoc($ids, \array_unique($ids))), $metadata->getEntityMetadata()->getClassName(), $dto::class));
        }

        return \array_combine($ids, $arrayFromCollection);
    }

    /**
     * @param class-string $entity
     *
     * @throws NotExistAttribute
     */
    private function getEntity(object $dto, string $entity, Metadata $metadata, ?DtoIdentifier $dtoIdentifier = null): object
    {
        $entityFromDto = $metadata->getEntityMetadata()->getClassName();

        if ($entity !== $entityFromDto) {
            throw new NotExistAttribute(\sprintf('Entity from class "%s" and defined entity in entity attribute "%s" in DTO object %s is not same!', $entity, $entityFromDto, $dto::class));
        }

        if (!$dtoIdentifier instanceof DtoIdentifier) {
            $dtoIdentifier = $this->getDtoIdentifier($dto, $metadata);
        }

        if (null === $dtoIdentifier->getValue()) {
            return new $entity();
        }

        if ($metadata->getEntityMetadata()->isNewEntity()) {
            $this->newEntity($dtoIdentifier, $entity);
        }

        // If DTO have ID, then find in database. Action is edit entity with DTO.
        // Else then create new Entity with this ID.
        return $this->repository->findByIdentifier($dtoIdentifier->getValue(), $entity) ?? $this->newEntity($dtoIdentifier, $entity);
    }

    /**
     * @phpstan-return array<int|string, object>
     */
    private function getPreviousCollection(object $entity, Property $property): array
    {
        /**
         * var  ArrayCollection $arrayCollection.
         *
         * @psalm-var ArrayCollection<int, object>
         */
        $arrayCollection = $this->defaultAccessor->callGetter($entity, $property);

        /** @var IdentifierInterface[] $previousArrayFromDatabase */
        $previousArrayFromDatabase = $arrayCollection->toArray();

        if (!\is_array($previousArrayFromDatabase) || false === (bool) $previousArrayFromDatabase) {
            return [];
        }

        $previousCollectionIds = \array_map(static fn (IdentifierInterface $entity): int|string => $entity->getIdentifier(), $previousArrayFromDatabase);

        return \array_combine($previousCollectionIds, $previousArrayFromDatabase);
    }

    /**
     * @throws NotExistAttribute
     */
    private function getDtoIdentifier(object $dto, Metadata $metadataDto): DtoIdentifier
    {
        foreach ($metadataDto->getProperties() as $property) {
            if (true !== $property->isIdentifier()) {
                continue;
            }

            if ($property->hasTransformerMetadata()) {
                /** @var int|string|null $value */
                $value = $this->transformer->reverse($dto, $property);

                return DtoIdentifier::create($metadataDto->getEntityMetadata()->getClassName(), $property, $value);
            }

            /** @var int|string|null $value */
            $value = $this->defaultAccessor->getValue($dto, $property);

            return DtoIdentifier::create($metadataDto->getEntityMetadata()->getClassName(), $property, $value);
        }

        throw new NotExistAttribute(\sprintf('IdentifierStrategy attribute is must be define (example: #[Identifier]) on property ID in object %s.', $dto::class));
    }

    private function newEntity(DtoIdentifier $dtoIdentifier, string $entityClassName): object
    {
        $idValue = [
            $dtoIdentifier->getProperty()->getName() => $dtoIdentifier->getValue(),
        ];

        return new $entityClassName(...$idValue);
    }

    /**
     * @throws \ReflectionException
     */
    public function getMetadata(object $dto): Metadata
    {
        // if not set => create dtosMetadata
        if ([] === $this->dtosMetadata) {
            $this->dtosMetadata = $this->reflectionMetadata->getDtosMetadata($dto);
        }

        // Not exist => add dtosMetadata
        if (!isset($this->dtosMetadata[$dto::class])) {
            $this->dtosMetadata = [...$this->dtosMetadata, ...$this->reflectionMetadata->getDtosMetadata($dto)];
        }

        /** @var Metadata $metadata */
        $metadata = $this->dtosMetadata[$dto::class];

        return $metadata;
    }
}