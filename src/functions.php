<?php

declare(strict_types=1);

namespace Zjk\DtoMapper;

/**
 * @param 'boolean'|'integer'|'double'|'string'|'array'|'object'|'resource'|'NULL'|'unknown type'|'resource (closed)' $type
 * @param array<array{}|object>                                                                                       $inputCollections
 */
function checkIsAllValuesInArraySameType(
    array $inputCollections,
    string $type
): void {
    $isAllValueInArraySameType = static fn (array $inputCollection): bool => !(bool) \array_filter($inputCollection, static fn ($value): bool => \gettype($value) !== $type);

    if (false === $isAllValueInArraySameType($inputCollections)) {
        throw new \RuntimeException('All value in array is not same type.');
    }
}

/**
 * Ensure array from iterable.
 *
 * @template T
 *
 * @param iterable<T> $iterable
 *
 * @return array<T>
 */
function iterable_to_array(iterable $iterable, bool $preserveKeys = true): array
{
    if (\is_array($iterable)) {
        return $preserveKeys ? $iterable : \array_values($iterable);
    }

    return \iterator_to_array($iterable, $preserveKeys);
}
