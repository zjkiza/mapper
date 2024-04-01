<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Exception;

use Zjk\DtoMapper\Contract\DataTransformerInterface;

final class TransformationFailedException extends \RuntimeException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function transform(mixed $input, DataTransformerInterface $transformer, ?\Throwable $previous = null): self
    {
        return self::create($input, $transformer, $previous, 'transform');
    }

    public static function reverse(mixed $input, DataTransformerInterface $transformer, ?\Throwable $previous = null): self
    {
        return self::create($input, $transformer, $previous, 'reverse');
    }

    private static function create(mixed $input, DataTransformerInterface $transformer, ?\Throwable $previous, string $direction): self
    {
        $inputType = \get_debug_type($input);
        $directionMessage = 'reverse' === $direction ? 'reverse transform' : 'transform';
        $message = \sprintf('Unable to %s value of type "%s" using "%s" transformer.', $directionMessage, $inputType, $transformer::class);

        return new self($message, $previous);
    }
}
