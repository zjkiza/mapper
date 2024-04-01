<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\PropertyAttributeInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Setter implements PropertyAttributeInterface
{
    public function __construct(public string $methodName)
    {
    }
}
