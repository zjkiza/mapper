<?php

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Transformer\UuidTransformer;

#[Entity(name: Expert::class)]
class ExpertResponseGetDto
{
    #[Transformer(name: UuidTransformer::class)]
    public ?string $id = null;

    #[Getter(methodName: 'getTitle')]
    public ?string $title = null;

    public ?string $name = null;
}
