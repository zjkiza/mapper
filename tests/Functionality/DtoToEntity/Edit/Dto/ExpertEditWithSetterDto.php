<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Edit\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\Setter;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[Entity(name: Expert::class)]
final class ExpertEditWithSetterDto
{
    public function __construct(
        #[Setter('setName')]
        public ?string $foo = null,
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
