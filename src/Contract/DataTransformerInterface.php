<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Contract;

/**
 * Transforms a value between different representations.
 */
interface DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     * Entity -> Dto.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * throws TransformationFailedException when the transformation fails
     */
    public function transform(mixed $value): mixed;

    /**
     * Transforms a value from the transformed representation to its original representation.
     * Dto -> Entity.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * throws TransformationFailedException when the transformation fails
     */
    public function reverse(mixed $value): mixed;
}
