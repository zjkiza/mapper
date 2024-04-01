<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Metadata;

use Zjk\DtoMapper\Contract\TransformerMetadataFactory;

final readonly class TransformerMetadata implements TransformerMetadataFactory
{
    public function __construct(
        /**
         * @phpstan-param class-string $actionClass
         */
        private string $actionClass
    ) {
    }

    public function actionName(): string
    {
        return $this->actionClass;
    }

    public static function create(string $actionClass): self
    {
        return new self($actionClass);
    }
}
