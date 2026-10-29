<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\IdentifierInterface;
use Zjk\DtoMapper\Contract\RepositoryInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Exception\RuntimeException;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\RelationMetadata;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

use function Zjk\DtoMapper\checkIsAllValuesInArraySameType;
use function Zjk\DtoMapper\iterable_to_array;

final readonly class RelationResolver
{
    public function __construct(
        private RepositoryInterface $repository,
        private DefaultAccessorInterface $defaultAccessor,
        private IdentifierResolver $identifierResolver,
    ) {
    }

    /**
     * @throws NotExistAttribute
     */
    public function resolve(object $entity, Property $property, object $dto): void
    {
        $localAction = $property->getLocalActionMetadata();
        if (!$localAction instanceof RelationMetadata) {
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

        $previousCollection = $this->getPreviousCollection($entity, $property);

        /** @var array<int|string, object> $newCollection */
        $newCollection = \array_values($inputCollections);

        $this->syncCollection($entity, $repositoryMetadata, $newCollection, $previousCollection);
    }

    /**
     * @param array<int|string, object> $newCollection
     * @param array<int|string, object> $previousCollection
     */
    public function syncCollection(object $entity, RepositoryMetadata $metadata, array $newCollection, array $previousCollection): void
    {
        $previousKeys = \array_flip(\array_keys($previousCollection));
        $newKeys = \array_flip(\array_keys($newCollection));

        foreach ($newCollection as $key => $newEntity) {
            if (isset($previousKeys[$key])) {
                $this->callMethod($entity, $metadata->getRemoveMethod(), $previousCollection[$key]);
            }

            $this->callMethod($entity, $metadata->getAddMethod(), $newEntity);
        }

        foreach ($previousCollection as $key => $prevEntity) {
            if (!isset($newKeys[$key])) {
                $this->callMethod($entity, $metadata->getRemoveMethod(), $prevEntity);
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    private function callMethod(object $entity, string $method, object $argument): void
    {
        $callable = [$entity, $method];

        if (!\is_callable($callable)) {
            throw new RuntimeException(\sprintf('Method "%s::%s()" does not exist or is not public.', $entity::class, $method));
        }

        $callable($argument);
    }

    /**
     * @return array<int|string, object>
     */
    public function getPreviousCollection(object $entity, Property $property): array
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

    /**
     * @template T of object
     *
     * @param iterable<T> $inputCollections
     *
     * @return array<string|int, T>
     *
     * @throws \ReflectionException
     * @throws NotExistAttribute
     */
    public function buildRelationCollection(RepositoryMetadata $repositoryMetadata, iterable $inputCollections, object $dto): array
    {
        /** @var class-string $class */
        $class = $repositoryMetadata->getClassName();

        $inputArray = iterable_to_array($inputCollections);

        /** @var T|false $first */
        $first = \reset($inputArray);

        if (false === $first) {
            return [];
        }

        /** @var array<int|string> $ids */
        $ids = \array_filter(
            \array_map(fn (object $dto): mixed => $this->identifierResolver->resolve($dto)->getValue(), $inputArray),
            static fn (mixed $value): bool => null !== $value
        );

        $collection = $this->repository->findAllByIdentifiers($ids, $class);

        /** @var T[] $arrayFromCollection */
        $arrayFromCollection = iterable_to_array($collection);

        if (\count($ids) !== \count($arrayFromCollection)) {
            throw new RuntimeException(\sprintf('There are duplicate entities with ids "%s" in collection "%s". In DTO "%s", you must insert a validator to check for duplicates in the input collection.', \implode(', ', \array_diff_assoc($ids, \array_unique($ids))), $repositoryMetadata->getClassName(), $dto::class));
        }

        return \array_combine($ids, $arrayFromCollection);
    }
}
