<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Builder;
use Ghostwriter\Plex\Interface\BuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

#[CoversClass(Builder::class)]
final class BuilderTest extends AbstractTestCase
{
    /**
     * @param array<non-empty-string, non-empty-string> $regularExpressions
     * @param non-empty-string                          $expected
     *
     * @throws Throwable
     */
    #[DataProvider('provideBuilder')]
    public function testBuild(array $regularExpressions, string $expected): void
    {
        self::assertSame($expected, Builder::new($regularExpressions)->build());
    }

    /**
     * @throws Throwable
     */
    public function testBuilderAlternation(): void
    {
        $builder = Builder::new();

        self::assertSame('BLM|BlackLivesMatter', $builder->alternation('BLM', 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderAtLeast(): void
    {
        $builder = Builder::new();

        self::assertSame('[BlackLivesMatter]{1,}', $builder->atLeast(1, 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderAtMost(): void
    {
        $builder = Builder::new();

        self::assertSame('[BlackLivesMatter]{,1}', $builder->atMost(1, 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderConcatenation(): void
    {
        self::assertSame('BLMBlackLivesMatter', Builder::new()->concatenation('BLM', 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderDefine(): void
    {
        $builder = Builder::new();

        $builder->add('BLM', 'BlackLivesMatter');

        self::assertSame([
            'BLM' => 'BlackLivesMatter',
        ], $builder->regularExpressions());
    }

    /**
     * @throws Throwable
     */
    public function testBuilderExactly(): void
    {
        self::assertSame('[BlackLivesMatter]{1}', Builder::new()->exactly(1, 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderGroup(): void
    {
        self::assertSame('(?:BlackLivesMatter)', Builder::new()->group('BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderInstanceOfBuilderInterface(): void
    {
        self::assertInstanceOf(BuilderInterface::class, Builder::new());
    }

    /**
     * @throws Throwable
     */
    public function testBuilderMark(): void
    {
        self::assertSame('(?:BlackLivesMatter)(*MARK:BLM)', Builder::new()->mark('BLM', 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderOneOrMore(): void
    {
        self::assertSame('(?:BlackLivesMatter)+', Builder::new()->oneOrMore('BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderOption(): void
    {
        self::assertSame('[BLMBlackLivesMatter]', Builder::new()->option('BLM', 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderOptional(): void
    {
        self::assertSame('(?:BlackLivesMatter)?', Builder::new()->optional('BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderRange(): void
    {
        self::assertSame('[BlackLivesMatter]{1,2}', Builder::new()->range(1, 2, 'BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderReference(): void
    {
        self::assertSame('(?&BlackLivesMatter)', Builder::new()->reference('BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderRegularExpressions(): void
    {
        self::assertSame([], Builder::new()->regularExpressions());

        $regularExpressions = [
            'A' => 'a',
            'B' => 'b',
        ];

        self::assertSame($regularExpressions, Builder::new($regularExpressions)->regularExpressions());
    }

    /**
     * @throws Throwable
     */
    public function testBuilderRepetition(): void
    {
        self::assertSame('{BlackLivesMatter}', Builder::new()->repetition('BlackLivesMatter'));
    }

    /**
     * @throws Throwable
     */
    public function testBuilderZeroOrMore(): void
    {
        self::assertSame('(?:BlackLivesMatter)*', Builder::new()->zeroOrMore('BlackLivesMatter'));
    }
}
