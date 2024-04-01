<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Settings;

use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Dto;

final class Settings
{
    public const GET = 'get';

    public const SET = 'set';

    public const MAPPER_LOCAL_FUNCTION = [
        Collection::class => 'fromCollectionEntityToDto',
        Dto::class => 'fromObjectEntityToDto',
    ];

    public static function getter(string $name): string
    {
        return \sprintf('%s%s', self::GET, \ucfirst($name));
    }

    public static function setter(string $name): string
    {
        return \sprintf('%s%s', self::SET, \ucfirst($name));
    }
}
