<?php

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Image;
use Zjk\DtoMapper\Transformer\UpperTransformer;

#[Entity(name: Image::class)]
class ImageResponseDto
{
    #[Getter(methodName: 'getIdentifier')]
    public ?string $id = null;

    #[Transformer(name: UpperTransformer::class)]
    public ?string $name = null;
}
