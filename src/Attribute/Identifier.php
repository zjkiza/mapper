<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Attribute;

use Zjk\DtoMapper\Contract\PropertyAttributeInterface;

/**
 * If used IdentifierStrategy, then the setter is not used to access the ID only __construct($id).
 * Through it the mapper knows to use that field for ID.
 *
 * If define ID in DTO. For new entity this ID will be used.
 *
 * If not send ID, then you need to define that your ID is generated automatically.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Identifier implements PropertyAttributeInterface
{
}
