<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Reader;

use Symfony\Contracts\Service\ResetInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use Zjk\DtoMapper\Utils\ReflectionUtils;

final class MetadataReader implements MetadataReaderInterface, ResetInterface
{
    /**
     * @var array<class-string, Metadata>
     */
    private static array $cache = [];

    public function __construct(
        private readonly ReflectionMetadata $reflectionMetadata,
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T $dto
     *
     * @throws \ReflectionException
     */
    #[\Override]
    public function getMetadata(object|string $dto): Metadata
    {
        $dtoClassString = \is_object($dto) ? $dto::class : $dto;

        if (!isset(self::$cache[$dtoClassString])) {
            self::$cache = [...self::$cache, ...$this->reflectionMetadata->getDtosMetadata($dto)];
        }

        /** @var Metadata $metadata */
        $metadata = self::$cache[$dtoClassString];

        return $metadata;
    }

    #[\Override]
    public function reset(): void
    {
        self::$cache = [];
        $this->reflectionMetadata->reset();
        ReflectionUtils::reset();
    }
}
