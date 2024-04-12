<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\WeakObject\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Image;

#[Entity(name: Image::class)]
final class ImageDto
{
    public function __construct(
        #[Identifier]
        public ?string $id = null,
        public ?string $name = null,
    ) {
    }
}
