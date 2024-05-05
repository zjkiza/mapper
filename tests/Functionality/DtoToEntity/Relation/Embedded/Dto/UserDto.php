<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\Embedded\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\User;
use Zjk\DtoMapper\Transformer\UuidTransformer;

#[Entity(User::class)]
final class UserDto
{
    public function __construct(
        #[Identifier]
        #[Transformer(UuidTransformer::class)]
        public ?string $id = null,
        public ?string $name = null,
    ) {
    }
}
