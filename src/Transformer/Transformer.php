<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Transformer;

use Zjk\DtoMapper\Contract\DataTransformerInterface;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\TransformerMetadataFactory;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Exception\InvalidObjectInstanceException;
use Zjk\DtoMapper\Exception\RuntimeException;
use Zjk\DtoMapper\Exception\TransformationFailedException;
use Zjk\DtoMapper\Metadata\Property;
use Zjk\DtoMapper\Metadata\TransformerMetadata;

final class Transformer implements TransformerInterface
{
    /**
     * @var array<int|string, DataTransformerInterface>
     */
    private array $transformers;

    /**
     * @param iterable<int|string, DataTransformerInterface> $transformers
     */
    public function __construct(
        private readonly DefaultAccessorInterface $defaultAccessor,
        iterable $transformers
    ) {
        $this->transformers = $transformers instanceof \Traversable ? \iterator_to_array($transformers, false) : $transformers;
    }

    public function transform(object $entity, Property $property): mixed
    {
        $value = $this->defaultAccessor->callGetter($entity, $property);

        if (null === $value) {
            return null;
        }

        try {
            \assert($property->getTransformerMetadata() instanceof TransformerMetadata);

            $transformValue = $this->getTransformer($property->getTransformerMetadata())->transform($value);
        } catch (TransformationFailedException $exception) {
            throw new RuntimeException(\sprintf('In entity %s for property %s. %s', $entity::class, $property->getGetter(), $exception->getMessage()));
        }

        return $transformValue;
    }

    public function reverse(object $dto, Property $property): mixed
    {
        $value = $this->defaultAccessor->getValue($dto, $property);

        if (null === $value) {
            return null;
        }

        \assert($property->getTransformerMetadata() instanceof TransformerMetadata);

        return $this->getTransformer($property->getTransformerMetadata())->reverse($value);
    }

    private function getTransformer(TransformerMetadataFactory $factory): DataTransformerInterface
    {
        /**
         * @var class-string $action
         */
        $action = $factory->actionName();

        if (!isset($this->transformers[$action])) {
            throw new InvalidObjectInstanceException(\sprintf('Object with instance "%s" does not exist! Check that you have imported that instance into a Mapper class constructor.', $action));
        }

        if (!$this->transformers[$action] instanceof DataTransformerInterface) {
            throw new InvalidObjectInstanceException(\sprintf('Incorrect instance class. "%s" does not implement the %s', $action, DataTransformerInterface::class));
        }

        return $this->transformers[$action];
    }
}
