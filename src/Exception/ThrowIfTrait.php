<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Exception;

trait ThrowIfTrait
{
    public static function throwIf(mixed $condition, string $message = ''): void
    {
        if ($condition) {
            throw new self($message);
        }
    }
}
