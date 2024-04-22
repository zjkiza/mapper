<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Reader;

use ReflectionException;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;

final class MetadataReader implements MetadataReaderInterface
{
    /**
     * @var array<class-string, Metadata>
     */
    private static array $cacheDtosMetadata = [];

    public function __construct(
        private readonly ReflectionMetadata $reflectionMetadata
    )
    {
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T $dto
     *
     * @throws ReflectionException
     */
    public function getMetadata(object|string $dto): Metadata
    {
        // if not set => create dtosMetadata
        if ([] === self::$cacheDtosMetadata) {
            self::$cacheDtosMetadata = $this->reflectionMetadata->getDtosMetadata($dto);
        }

        $dtoClassString = is_object($dto) ? $dto::class : $dto;

        // Not exist => add dtosMetadata
        if (!isset(self::$cacheDtosMetadata[$dtoClassString])) {
            self::$cacheDtosMetadata = [...self::$cacheDtosMetadata, ...$this->reflectionMetadata->getDtosMetadata($dto)];
        }

        /** @var Metadata $metadata */
        $metadata = self::$cacheDtosMetadata[$dtoClassString];

        return $metadata;
    }
}
