<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Transformer\UpperTransformer;

#[Entity(name: Media::class)]
class MediaResponseDto
{
    #[Getter(methodName: 'getIdentifier')]
    public ?string $id = null;

    #[Transformer(name: UpperTransformer::class)]
    public ?string $title = null;

    #[Dto(UserResponseDto::class)]
    public ?UserResponseDto $user = null;

    public ?string $description = null;

    #[Collection(className: ImageResponseDto::class)]
    public ?array $image = null;

    #[Collection(className: ExpertResponseDto::class)]
    public ?array $expert = null;
}
