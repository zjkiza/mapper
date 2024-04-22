<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Reader;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Metadata\Metadata;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use function md5;
use function sprintf;

final readonly class CachedMetadataReader implements MetadataReaderInterface
{
    private ReflectionMetadata $reflectionMetadata;

    private CacheItemPoolInterface $cache;

    public function __construct(
        ReflectionMetadata      $reflectionMetadata,
        ?CacheItemPoolInterface $cache = null,
    )
    {
        $this->reflectionMetadata = $reflectionMetadata;
        $this->cache = $cache ?? new ArrayAdapter();
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T $dto
     *
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function getMetadata(object|string $dto): Metadata
    {
        /** @var class-string $dtoClassString */
        $dtoClassString = is_object($dto) ? $dto::class : $dto;

        $item = $this->cache->getItem(
            $this->getKey($dtoClassString)
        );

        if ($item->isHit()) {
            /** @var Metadata $metadata */
            $metadata = $item->get();

            return $metadata;
        }

        $metadata = null;
        $metadataDtos = $this->reflectionMetadata->getDtosMetadata($dto);

        foreach ($metadataDtos as $metadataDto) {

            $itemLocal = $this->cache->getItem(
                $this->getKey($metadataDto->getClassNameDto())
            );

            if ($itemLocal->isHit()) {
                continue;
            }

            $itemLocal->set($metadataDto);
            $this->cache->save($itemLocal);

            if ($dtoClassString === $metadataDto->getClassNameDto()) {
                $metadata = $metadataDto;
            }
        }

        assert($metadata instanceof Metadata);

        return $metadata;
    }

    /**
     * @param class-string $dtoClassString
     */
    private function getKey(string $dtoClassString): string
    {
        return sprintf('MetadataDto_%s', md5($dtoClassString));
    }
}
