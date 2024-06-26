<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

interface RelationAttributeInterface
{
    /**
     * @return class-string
     */
    public function getClassNameDto(): string;
}
