<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Transformer;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Zjk\DtoMapper\Contract\DataTransformerInterface;
use Zjk\DtoMapper\Exception\TransformationFailedException;

final class UuidTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof UuidInterface) {
            return $value->toString();
        }

        throw TransformationFailedException::transform($value, $this);
    }

    public function reverse(mixed $value): ?UuidInterface
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                return Uuid::fromString($value);
            } catch (\Exception $exception) {
                throw TransformationFailedException::reverse($value, $this, $exception);
            }
        }

        throw TransformationFailedException::reverse($value, $this);
    }
}
