<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Zjk\DtoMapper\Contract\RepositoryInterface;

use function Zjk\DtoMapper\iterable_to_array;

final readonly class DoctrineRepositoryRegistry implements RepositoryInterface
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    public function findByIdentifier(mixed $identifier, string $entity): ?object
    {
        return $this->doctrine
            ->getRepository($entity)
            ->findOneBy(['id' => $identifier]);
    }

    public function findAllByIdentifiers(iterable $identifiers, string $entity): iterable
    {
        $identifiers = iterable_to_array($identifiers);

        return $this->doctrine
            ->getRepository($entity)
            ->findBy(['id' => $identifiers]);
    }
}
