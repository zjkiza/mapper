<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Ignore;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Transformer\UuidTransformer;

#[Entity(name: Media::class)]
class MediaResponseGetAndUuidDto
{
    #[Transformer(name: UuidTransformer::class)]
    public ?string $id = null;

    #[Getter(methodName: 'getDescription')]
    public ?string $text = null;

    #[Ignore]
    public ?string $title = 'bar';
}
