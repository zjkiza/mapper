<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Edit\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\Transformer;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Transformer\UpperTransformer;

#[Entity(name: Expert::class)]
final class ExpertEditDto
{
    public function __construct(
        #[Transformer(UpperTransformer::class)]
        public ?string $name = null,
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
