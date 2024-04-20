<?php

declare(strict_types=1);


namespace Zjk\DtoMapper\Contract;

use Zjk\DtoMapper\Metadata\Metadata;

interface MetadataReaderInterface
{
    public function getMetadata(object|string $dto): Metadata;
}