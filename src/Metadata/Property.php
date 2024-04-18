<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final readonly class Property
{
    public function __construct(
        private string               $getter,
        private string               $setter,
        private string               $name,
        private bool                 $identifier,
        private ?TransformerMetadata $transformerMetadata = null,
        private ?RelationMetadata    $localActionMetadata = null,
        private ?RepositoryMetadata  $repositoryMetadata = null
    ) {
    }

    public function getGetter(): string
    {
        return $this->getter;
    }

    public function getSetter(): string
    {
        return $this->setter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isIdentifier(): bool
    {
        return $this->identifier;
    }

    public function hasTransformerMetadata(): bool
    {
        return $this->transformerMetadata instanceof TransformerMetadata;
    }

    public function getTransformerMetadata(): ?TransformerMetadata
    {
        return $this->transformerMetadata;
    }

    public function hasLocalActionMetadata(): bool
    {
        return $this->localActionMetadata instanceof RelationMetadata;
    }

    public function getLocalActionMetadata(): ?RelationMetadata
    {
        return $this->localActionMetadata;
    }

    public function getRepositoryMetadata(): ?RepositoryMetadata
    {
        return $this->repositoryMetadata;
    }

    public function hasRepositoryMetadata(): bool
    {
        return $this->repositoryMetadata instanceof RepositoryMetadata;
    }

    public static function create(
        string               $getter,
        string               $setter,
        string               $name,
        bool                 $identifier,
        ?TransformerMetadata $transformerMetadata = null,
        ?RelationMetadata    $localActionMetadata = null,
        ?RepositoryMetadata  $repositoryMetadata = null,
    ): self {
        return new self($getter, $setter, $name, $identifier, $transformerMetadata, $localActionMetadata, $repositoryMetadata);
    }
}
