<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class FileCoder
{
    public function __construct(public string $name)
    {
    }
}
