<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Transformer;

trait IndexTransformer
{
    public static function getIndex(): string
    {
        return self::class;
    }
}
