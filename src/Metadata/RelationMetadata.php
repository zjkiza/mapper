<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

use Zjk\DtoMapper\Contract\RelationAttributeInterface;
use Zjk\DtoMapper\Settings\Settings;

final readonly class RelationMetadata
{
    public function __construct(
        private string $strategy,
        private string $functionName,
        private string $classNameDto
    ) {
    }

    public function getStrategy(): string
    {
        return $this->strategy;
    }

    public function actionName(): string
    {
        return $this->functionName;
    }

    public function getClassNameDto(): string
    {
        return $this->classNameDto;
    }

    public static function create(string $strategy, RelationAttributeInterface $relationAttributeInstance): self
    {
        return new self(
            $strategy,
            Settings::MAPPER_LOCAL_FUNCTION[$relationAttributeInstance::class],
            $relationAttributeInstance->getClassNameDto()
        );
    }
}
