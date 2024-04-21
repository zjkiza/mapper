<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Reader;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use function md5;
use function sprintf;

final readonly class CachedMetadataReader implements MetadataReaderInterface
{
    public function __construct(
        private ReflectionMetadata     $reflectionMetadata,
        private CacheItemPoolInterface $cache,
    )
    {
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function getMetadata(object|string $dto): Metadata
    {
        $dtoClassString = is_object($dto) ? get_class($dto) : $dto;

        $item = $this->cache->getItem(
            $this->getKey($dtoClassString)
        );

        if (true === $item->isHit()) {
            return $item->get();
        }

        $metadata = null;
        $metadataDtos = $this->reflectionMetadata->getDtosMetadata($dto);

        foreach ($metadataDtos as $metadataDto) {

            $itemLocal = $this->cache->getItem(
                $this->getKey($metadataDto->getClassNameDto())
            );

            if (true === $itemLocal->isHit()) {
                continue;
            }

            $itemLocal->set($metadataDto);
            $this->cache->save($itemLocal);

            if ($dtoClassString === $metadataDto->getClassNameDto()) {
                $metadata = $metadataDto;
            }
        }

        return $metadata;
    }

    public function getKey(object|string $dtoClassString): string
    {
        return sprintf('MetadataDto_%s', md5($dtoClassString));
    }
}
