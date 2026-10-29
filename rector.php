<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // same exclusions as in phpstan.neon.dist (generated cache and DTO fixtures)
    $rectorConfig->skip([
        __DIR__ . '/tests/Resources',
        __DIR__ . '/tests/Functionality/EntityToDto/Dto',
        __DIR__ . '/tests/Functionality/DtoToEntity/Relation/OnlyRelation/Dto',
        __DIR__ . '/tests/Functionality/DtoToEntity/Relation/WeakObject/Dto',
        __DIR__ . '/tests/Functionality/DtoToEntity/Edit/Dto',
        __DIR__ . '/tests/Functionality/DtoToEntity/Create/Dto',
    ]);

    //define sets of rules
    // SetList::STRICT_BOOLEANS was removed in Rector 2 and EARLY_RETURN is part of CODE_QUALITY since then
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
    ]);
};
