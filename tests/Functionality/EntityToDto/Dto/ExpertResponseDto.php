<?php

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Transformer\UpperTransformer;

#[Entity(name: Expert::class)]
class ExpertResponseDto
{
    #[Getter(methodName: 'getIdentifier')]
    public ?string $id = null;

    public ?string $title = null;

    #[Transformer(name: UpperTransformer::class)]
    public ?string $name = null;
}
