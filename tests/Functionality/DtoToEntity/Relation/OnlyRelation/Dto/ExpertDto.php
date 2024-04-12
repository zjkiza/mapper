<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\OnlyRelation\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[Entity(name: Expert::class)]
final class ExpertDto
{
    public function __construct(
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
