<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Exception;

use Zjk\DtoMapper\Contract\ExceptionInterface;

final class InvalidObjectInstanceException extends \RuntimeException implements ExceptionInterface
{
}
