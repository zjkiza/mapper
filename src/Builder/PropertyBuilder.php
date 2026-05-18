<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Builder;

use Zjk\DtoMapper\Metadata\RelationMetadata;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\RepositoryMetadata;
use Zjk\DtoMapper\Metadata\TransformerMetadata;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PropertyBuilder
{
    private string $getter;
    private string $setter;
    private string $name;
    private bool $identifier = false;

    private ?TransformerMetadata $transformerMetadata = null;
    private ?RelationMetadata $localActionMetadata = null;
    private ?RepositoryMetadata $repositoryMetadata = null;

    private function __construct()
    {
    }

    public function setGetter(string $getter): self
    {
        $this->getter = $getter;

        return $this;
    }

    public function setSetter(string $setter): self
    {
        $this->setter = $setter;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setIdentifier(bool $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function setTransformerMetadata(?TransformerMetadata $transformerMetadata): self
    {
        $this->transformerMetadata = $transformerMetadata;

        return $this;
    }

    public function setLocalActionMetadata(?RelationMetadata $localActionMetadata): self
    {
        $this->localActionMetadata = $localActionMetadata;

        return $this;
    }

    public function setRepositoryMetadata(?RepositoryMetadata $repositoryMetadata): self
    {
        $this->repositoryMetadata = $repositoryMetadata;

        return $this;
    }

    public static function create(): self
    {
        return new self();
    }

    public function build(): Property
    {
        return Property::create(
            $this->getter,
            $this->setter,
            $this->name,
            $this->identifier,
            $this->transformerMetadata,
            $this->localActionMetadata,
            $this->repositoryMetadata,
        );
    }
}
