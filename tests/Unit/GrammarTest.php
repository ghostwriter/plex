<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Grammar;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

#[CoversClass(Grammar::class)]
final class GrammarTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    #[DataProvider('provideRegularExpressions')]
    public function testCompileRegularExpressions(array $regularExpressions, string $expected): void
    {
        self::assertSame($expected, Grammar::new($regularExpressions)->compile());
    }
}
