<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final readonly class RepositoryMetadata
{
    public function __construct(
        private string $className,
        private string $addMethod,
        private string $removeMethod,
        /**
         * Only a relation to another entity. There is no CRUD capability behind it.
         */
        private bool $onlyRelation = false
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getAddMethod(): string
    {
        return $this->addMethod;
    }

    public function getRemoveMethod(): string
    {
        return $this->removeMethod;
    }

    public function isOnlyRelation(): bool
    {
        return $this->onlyRelation;
    }

    public static function create(string $className, string $addMethod, string $removeMethod, bool $onlyRelation = false): self
    {
        return new self($className, $addMethod, $removeMethod, $onlyRelation);
    }
}
