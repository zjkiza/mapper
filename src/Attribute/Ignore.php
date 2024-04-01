<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\AttributeInterface;

/**
 * Ignored property.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Ignore implements AttributeInterface
{
}
