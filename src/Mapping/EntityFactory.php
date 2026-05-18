<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Zjk\DtoMapper\Contract\IdentifierInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\RepositoryInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Exception\TypeError;
use Zjk\DtoMapper\Metadata\DtoIdentifier;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\RelationMetadata;

final readonly class EntityFactory
{
    public function __construct(
        private RepositoryInterface $repository,
        private MetadataReaderInterface $metadataReader,
    ) {
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @param class-string $entityClassName
     */
    public function create(DtoIdentifier $dtoIdentifier, string $entityClassName): object
    {
        $idValue = [
            $dtoIdentifier->getProperty()->getName() => $dtoIdentifier->getValue(),
        ];

        try {
            /** @psalm-suppress InvalidStringClass */
            return new $entityClassName(...$idValue);
        } catch (\TypeError) {
            throw new TypeError(\sprintf('A new entity cannot be created. Id argument in dto "%s" must by same type with entity. Use the transformer on the ID property to convert it to a type like in entity "%s".', $dtoIdentifier->getDtoClass(), $dtoIdentifier->getEntityClass()));
        }
    }

    /**
     * @param class-string               $entity
     * @param array<string, object>|null $entityLookupMap
     *
     * @throws NotExistAttribute
     */
    public function findOrCreate(object $dto, string $entity, Metadata $metadata, DtoIdentifier $dtoIdentifier, ?array $entityLookupMap = null): object
    {
        $entityFromDto = $metadata->getEntityMetadata()->getClassName();

        if ($entity !== $entityFromDto) {
            throw new NotExistAttribute(\sprintf('Entity from class "%s" and defined entity in entity attribute "%s" in DTO object %s is not same!', $entity, $entityFromDto, $dto::class));
        }

        $value = $dtoIdentifier->getValue();
        if (null === $value) {
            return new $entity();
        }

        if ($metadata->getEntityMetadata()->isNewEntity()) {
            $this->create($dtoIdentifier, $entity);
        }

        if (null !== $entityLookupMap) {
            \assert(\is_string($value) || \is_int($value));
            $lookupKey = $entity.':'.$value;

            return $entityLookupMap[$lookupKey] ?? $this->create($dtoIdentifier, $entity);
        }

        return $this->repository->findByIdentifier($value, $entity) ?? $this->create($dtoIdentifier, $entity);
    }

    /**
     * @phpstan-return class-string
     *
     * @throws \ReflectionException
     */
    public function getEntityFromRelationAttribute(Property $property): string
    {
        $localAction = $property->getLocalActionMetadata();
        \assert($localAction instanceof RelationMetadata);

        /** @var class-string $classNameDto */
        $classNameDto = $localAction->getClassNameDto();
        $metadata = $this->metadataReader->getMetadata($classNameDto);

        /** @var class-string $entityFromDto */
        $entityFromDto = $metadata->getEntityMetadata()->getClassName();

        return $entityFromDto;
    }

    /**
     * @param array<int|string>                               $identifiers
     * @param array<int|string, DtoToEntityBatchItem<object>> $dtoWithIdentifier
     *
     * @return array<string, IdentifierInterface>
     */
    public function buildLookupMap(array $identifiers, array $dtoWithIdentifier): array
    {
        if ([] === $identifiers) {
            return [];
        }

        /** @var array<class-string, list<int>> $groupedByIdentityClass */
        $groupedByIdentityClass = [];

        foreach ($dtoWithIdentifier as $index => $item) {
            $groupedByIdentityClass[$item->getEntityClass()][] = $index;
        }

        $lookupMap = [];

        foreach ($groupedByIdentityClass as $entityClass => $indexes) {
            $entityIdentifiers = \array_map(
                static function (int|string $index) use ($dtoWithIdentifier): int|string {
                    $value = $dtoWithIdentifier[$index]->getIdentifier()->getValue();
                    \assert(\is_int($value) || \is_string($value));

                    return $value;
                },
                $indexes
            );

            $entities = $this->repository->findAllByIdentifiers($entityIdentifiers, $entityClass);

            foreach ($entities as $foundEntity) {
                \assert($foundEntity instanceof IdentifierInterface);
                $lookupKey = $entityClass.':'.$foundEntity->getIdentifier();
                $lookupMap[$lookupKey] = $foundEntity;
            }
        }

        return $lookupMap;
    }
}
