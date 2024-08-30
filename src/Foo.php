<?php

declare(strict_types=1);

namespace Ghostwriter\Wip;

use Ghostwriter\Wip\Interface\FooInterface;

/** @see FooTest */
final class Foo implements FooInterface
{
    public function __construct()
    {
    }

    public function test(): bool
    {
        return true;
    }

    public static function new(): self
    {
        return new self();
    }
}
