<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\Embedded\Dto;

use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;

#[Entity(Media::class)]
final class MediaDto
{
    public function __construct(
        #[Identifier]
        public ?string $id = null,
        #[Dto(UserDto::class)]
        public ?UserDto $user = null,
    ) {
    }
}
