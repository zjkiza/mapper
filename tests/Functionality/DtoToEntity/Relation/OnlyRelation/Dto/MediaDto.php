<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\OnlyRelation\Dto;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Identifier;
use Zjk\DtoMapper\Attribute\RepositoryClass;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;

#[Entity(Media::class)]
final class MediaDto
{
    public function __construct(
        #[Identifier]
        public ?string $id = null,
        #[RepositoryClass(
            entityClassName: Expert::class,
            addMethod: 'addExpert',
            removeMethod: 'removeExpert',
            onlyRelation: true,
        )]
        #[Collection(className: ExpertDto::class)]
        public ?array $expert = null,
    ) {
    }
}
