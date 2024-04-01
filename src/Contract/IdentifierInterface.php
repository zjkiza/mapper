<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

interface IdentifierInterface
{
    public function getIdentifier(): int|string;
}
