<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Plex\Builder;
use Ghostwriter\Plex\Exception\UndefinedReferenceException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Throwable;

#[CoversClass(UndefinedReferenceException::class)]
#[UsesClass(Builder::class)]
final class UndefinedReferenceExceptionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testThrowUndefinedReferenceException(): void
    {
        $this->expectException(UndefinedReferenceException::class);
        $this->expectExceptionMessage('Undefined reference "B" detected in regular expression "(?:(?&B))(*MARK:A)"');

        $builder = Builder::new();

        $builder->add('A', '(?&B)');

        $builder->build();
    }
}
