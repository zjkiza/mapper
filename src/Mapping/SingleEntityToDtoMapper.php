<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;

final readonly class SingleEntityToDtoMapper
{
    public function __construct(
        private MetadataReaderInterface $metadataReader,
        private DefaultAccessorInterface $defaultAccessor,
        private TransformerInterface $transformer,
    ) {
    }

    public function map(object $entity, object|string $dto): object
    {
        if (!\is_object($dto)) {
            /** @var class-string $dtoClass */
            $dtoClass = $dto;
            $reflectionDto = new \ReflectionClass($dtoClass);
            $dto = $reflectionDto->newInstance();
        }

        $metadata = $this->metadataReader->getMetadata($dto);

        foreach ($metadata->getProperties() as $property) {
            if (true === $property->hasTransformerMetadata()) {
                $value = $this->transformer->transform($entity, $property);
                $this->defaultAccessor->setValue($dto, $property, $value);
                continue;
            }

            $localAction = $property->getLocalActionMetadata();
            if (null !== $localAction) {
                $strategy = $localAction->getStrategy();

                /** @var class-string $classNameDto */
                $classNameDto = $localAction->getClassNameDto();
                $entityValue = $this->defaultAccessor->callGetter($entity, $property);

                $value = $this->mapLocalAction($entityValue, $strategy, $classNameDto);

                $this->defaultAccessor->setValue($dto, $property, $value);
                continue;
            }

            $value = $this->defaultAccessor->callGetter($entity, $property);
            $this->defaultAccessor->setValue($dto, $property, $value);
        }

        return $dto;
    }

    /**
     * @param class-string $classNameDto
     */
    private function mapLocalAction(mixed $entityValue, string $strategy, string $classNameDto): mixed
    {
        if (Collection::class === $strategy) {
            return $this->mapCollectionEntityToDto($entityValue, $classNameDto);
        }

        if (Dto::class === $strategy) {
            if (!\is_object($entityValue)) {
                return $entityValue;
            }

            return $this->map($entityValue, $classNameDto);
        }

        return $entityValue;
    }

    /**
     * @param class-string $targetDto
     *
     * @return array<int, object>
     */
    private function mapCollectionEntityToDto(mixed $collection, string $targetDto): array
    {
        if (null === $collection) {
            return [];
        }

        if (!\is_iterable($collection)) {
            $collection = [$collection];
        }

        $result = [];

        foreach ($collection as $item) {
            if (!\is_object($item)) {
                continue;
            }
            $result[] = $this->map($item, $targetDto);
        }

        return $result;
    }
}
