<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\ClassAttributeInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class NewEntity implements ClassAttributeInterface
{
}
