<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\PropertyAttributeInterface;
use Zjk\DtoMapper\Contract\RelationAttributeInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Dto implements PropertyAttributeInterface, RelationAttributeInterface
{
    /** @var class-string */
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function getClassNameDto(): string
    {
        return $this->className;
    }
}
