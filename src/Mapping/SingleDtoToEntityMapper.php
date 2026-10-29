<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Doctrine\ORM\EntityManagerInterface;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Metadata\DtoIdentifier;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\Property;

final readonly class SingleDtoToEntityMapper
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MetadataReaderInterface $metadataReader,
        private DefaultAccessorInterface $defaultAccessor,
        private TransformerInterface $transformer,
        private EntityFactory $entityFactory,
        private IdentifierResolver $identifierResolver,
        private RepositoryRelationMapper $repositoryRelationMapper,
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
            $entity = $this->resolveEntity($dto, $entity, $metadata, $dtoIdentifier, $entityLookupMap);
        }

        foreach ($metadata->getProperties() as $property) {
            if (true === $property->isIdentifier()) {
                continue;
            }

            $this->mapProperty($dto, $entity, $property);
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
     * @param class-string               $entityClassName
     * @param array<string, object>|null $entityLookupMap
     */
    private function resolveEntity(object $dto, string $entityClassName, Metadata $metadata, DtoIdentifier $dtoIdentifier, ?array $entityLookupMap): object
    {
        if ($metadata->getEntityMetadata()->isNewEntity()) {
            return $this->entityFactory->create($dtoIdentifier, $entityClassName);
        }

        return $this->entityFactory->findOrCreate($dto, $entityClassName, $metadata, $dtoIdentifier, $entityLookupMap);
    }

    private function mapProperty(object $dto, object $entity, Property $property): void
    {
        if ($property->hasTransformerMetadata()) {
            $value = $this->transformer->reverse($dto, $property);
            $this->defaultAccessor->callSetter($entity, $property, $value);

            return;
        }

        if ($property->hasRepositoryMetadata()) {
            $this->repositoryRelationMapper->map(
                $entity,
                $property,
                $dto,
                fn (object $dto, object $entity): object => $this->map($dto, $entity)
            );

            return;
        }

        if (Dto::class === $property->getLocalActionMetadata()?->getStrategy()) {
            $this->mapEmbedded($dto, $entity, $property);

            return;
        }

        $value = $this->defaultAccessor->getValue($dto, $property);
        $this->defaultAccessor->callSetter($entity, $property, $value);
    }

    private function mapEmbedded(object $dto, object $entity, Property $property): void
    {
        /** @var object $objectDto */
        $objectDto = $this->defaultAccessor->getValue($dto, $property);
        $entityFromDto = $this->entityFactory->getEntityFromRelationAttribute($property);

        $embeddedEntity = $this->map($objectDto, $entityFromDto);
        $this->defaultAccessor->callSetter($entity, $property, $embeddedEntity);
    }
}
