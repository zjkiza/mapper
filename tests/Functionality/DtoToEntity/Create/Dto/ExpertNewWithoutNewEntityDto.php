<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[Entity(name: Expert::class)]
final class ExpertNewWithoutNewEntityDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
