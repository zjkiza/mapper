<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Mapping;

use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Metadata\DtoIdentifier;

final readonly class IdentifierResolver
{
    public function __construct(
        private MetadataReaderInterface $metadataReader,
        private DefaultAccessorInterface $defaultAccessor,
        private TransformerInterface $transformer,
    ) {
    }

    /**
     * @throws NotExistAttribute
     */
    public function resolve(object $dto): DtoIdentifier
    {
        $metadata = $this->metadataReader->getMetadata($dto);

        foreach ($metadata->getProperties() as $property) {
            if (true !== $property->isIdentifier()) {
                continue;
            }

            if ($property->hasTransformerMetadata()) {
                $value = $this->transformer->reverse($dto, $property);

                return DtoIdentifier::create($dto::class, $metadata->getEntityMetadata()->getClassName(), $property, $value);
            }

            /** @var int|string|null $value */
            $value = $this->defaultAccessor->getValue($dto, $property);

            return DtoIdentifier::create($dto::class, $metadata->getEntityMetadata()->getClassName(), $property, $value);
        }

        throw new NotExistAttribute(\sprintf('IdentifierStrategy attribute is must be define (example: #[Identifier]) on property ID in object %s.', $dto::class));
    }
}
