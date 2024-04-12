<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto;

use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\NewEntity;
use Zjk\DtoMapper\Attribute\Setter;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;

#[NewEntity]
#[Entity(name: Expert::class)]
final class ExpertNewWithSetterDto
{
    public function __construct(
        #[Setter(methodName: 'setName')]
        public ?string $isNotName = null,
        public ?string $title = null,
        #[Identifier]
        public ?string $id = null,
    ) {
    }
}
