<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Transformer;

use Zjk\DtoMapper\Contract\DataTransformerInterface;
use Zjk\DtoMapper\Exception\TransformationFailedException;

final class UpperTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            return \strtoupper($value);
        }

        throw TransformationFailedException::transform($value, $this);
    }

    public function reverse(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            return \strtoupper($value);
        }

        throw TransformationFailedException::reverse($value, $this);
    }
}
