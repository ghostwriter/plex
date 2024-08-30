<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Exception;

use Ghostwriter\Plex\Interface\ExceptionInterface;
use UnexpectedValueException;

final class UnexpectedCharacterException extends UnexpectedValueException implements ExceptionInterface
{
}
