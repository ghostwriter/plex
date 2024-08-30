<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Exception;

use Ghostwriter\Plex\Interface\ExceptionInterface;
use LogicException;

final class ShouldNotHappenException extends LogicException implements ExceptionInterface
{
}
