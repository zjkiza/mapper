<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Metadata\Metadata;

interface MetadataReaderInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T>|T $dto
     */
    public function getMetadata(object|string $dto): Metadata;
}
