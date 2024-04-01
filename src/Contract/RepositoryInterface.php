<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

interface RepositoryInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T> $entity
     *
     * @phpstan-return T|null
     */
    public function findByIdentifier(mixed $identifier, string $entity): ?object;

    /**
     * @template T of object
     *
     * @param iterable<string|int> $identifiers
     * @param class-string<T>      $entity
     *
     * @return iterable<T>
     */
    public function findAllByIdentifiers(iterable $identifiers, string $entity): iterable;
}
