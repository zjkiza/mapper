<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\RelationMetadata;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;

use function Zjk\DtoMapper\checkIsAllValuesInArraySameType;

/**
 * Maps a DTO property marked with the Repository attribute (a collection of related entities) to the entity.
 */
final readonly class RepositoryRelationMapper
{
    public function __construct(
        private DefaultAccessorInterface $defaultAccessor,
        private RelationResolver $relationResolver,
        private FullCollectionBuilder $fullCollectionBuilder,
    ) {
    }

    /**
     * @param \Closure(object, object): object $mapper maps the DTO (first argument) to the entity (second argument)
     *
     * @throws NotExistAttribute
     */
    public function map(object $entity, Property $property, object $dto, \Closure $mapper): void
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

        /** @var array<object> $inputCollections */
        $newCollection = $this->resolveNewCollection($repositoryMetadata, $inputCollections, $dto, $mapper);

        if (false === (bool) $newCollection) {
            $this->defaultAccessor->setValue($entity, $property, new ArrayCollection());

            return;
        }

        $previousCollection = $this->relationResolver->getPreviousCollection($entity, $property);

        $this->relationResolver->syncCollection($entity, $repositoryMetadata, $newCollection, $previousCollection);
    }

    /**
     * @param object[]                         $inputCollections
     * @param \Closure(object, object): object $mapper
     *
     * @return array<int|string, object>
     */
    private function resolveNewCollection(RepositoryMetadata $repositoryMetadata, array $inputCollections, object $dto, \Closure $mapper): array
    {
        if ($repositoryMetadata->isOnlyRelation()) {
            /** @var array<int|string, object> $newCollection */
            $newCollection = $this->relationResolver->buildRelationCollection($repositoryMetadata, $inputCollections, $dto);

            return $newCollection;
        }

        /** @var array<int|string, object> $newCollection */
        $newCollection = $this->fullCollectionBuilder->build($repositoryMetadata, $inputCollections, $mapper);

        return $newCollection;
    }
}
