<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\Ignore;
use Zjk\DtoMapper\Attribute\NewEntity;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[NewEntity]
#[Entity(name: Expert::class)]
final class ExpertNewWithIgnoreDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        #[Ignore]
        public ?string $lorem = null,
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
