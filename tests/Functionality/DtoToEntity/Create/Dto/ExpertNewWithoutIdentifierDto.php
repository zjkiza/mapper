<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\NewEntity;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[NewEntity]
#[Entity(name: Expert::class)]
final class ExpertNewWithoutIdentifierDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        public ?string $id = null,
    ) {
    }
}
