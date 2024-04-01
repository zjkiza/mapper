<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

final readonly class LocalActionMetadata
{
    public function __construct(
        private string $functionName,
        private string $className
    ) {
    }

    public function actionName(): string
    {
        return $this->functionName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public static function create(string $actionClass, string $className): self
    {
        return new self($actionClass, $className);
    }
}
