<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Exception;

use Ghostwriter\Plex\Interface\ExceptionInterface;
use InvalidArgumentException;

final class CircularReferenceException extends InvalidArgumentException implements ExceptionInterface
{
}
