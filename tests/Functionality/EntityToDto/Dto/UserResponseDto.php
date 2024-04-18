<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Tests\Resources\App\Entities\User;

#[Entity(User::class)]
final class UserResponseDto
{
    #[Getter(methodName: 'getIdentifier')]
    public ?string $id = null;

    public ?string $name = null;
}
