<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Transformer\UuidTransformer;

#[Entity(name: Expert::class)]
final class ExpertNewWithTransformerWithoutNewEntityDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        #[Identifier]
        #[Transformer(UuidTransformer::class)]
        public ?string $id = null,
    ) {
    }
}
