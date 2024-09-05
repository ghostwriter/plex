<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Builder;
use Ghostwriter\Plex\Grammar;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(Grammar::class)]
#[UsesClass(Builder::class)]
final class GrammarTest extends AbstractTestCase
{
    /**
     * @param array<non-empty-string, non-empty-string> $regularExpressions
     * @param non-empty-string                          $expected
     *
     * @throws Throwable
     */
    #[DataProvider('provideRegularExpressions')]
    public function testCompileRegularExpressions(array $regularExpressions, string $expected): void
    {
        self::assertSame($expected, Grammar::new($regularExpressions)->compile());
    }

    /**
     * @throws Throwable
     */
    public function testDefineRegularExpressions(): void
    {
        $builder = Builder::new([
            'semver' => '(?&major)\.(?&minor)\.(?&patch)(?&pre-release)?(?&build-metadata)?',
            'major' => '(?&number)',
            'minor' => '(?&number)',
            'patch' => '(?&number)',
            'pre-release' => '(?:-(?&identifier))',
            'build-metadata' => '(?:\+(?&identifier))',
            'number' => '_OVERWRITE_NUM_',
            'identifier' => '_OVERWRITE_ID_',
        ]);

        self::assertSame(
            '#(?|(?:_OVERWRITE_NUM_\._OVERWRITE_NUM_\._OVERWRITE_NUM_(?:-_OVERWRITE_ID_)?(?:\+_OVERWRITE_ID_)?)(*MARK:semver)|(?:_OVERWRITE_NUM_)(*MARK:major)|(?:_OVERWRITE_NUM_)(*MARK:minor)|(?:_OVERWRITE_NUM_)(*MARK:patch)|(?:(?:-_OVERWRITE_ID_))(*MARK:pre-release)|(?:(?:\+_OVERWRITE_ID_))(*MARK:build-metadata)|(?:_OVERWRITE_NUM_)(*MARK:number)|(?:_OVERWRITE_ID_)(*MARK:identifier))#Au',
            $builder->build()
        );

        $grammar = Grammar::new($builder->regularExpressions());

        $grammar->define('number', '\d+');
        $grammar->define('identifier', '[a-zA-Z0-9-]+');

        self::assertSame(
            '#(?|(?:\d+\.\d+\.\d+(?:-[a-zA-Z0-9-]+)?(?:\+[a-zA-Z0-9-]+)?)(*MARK:semver)|(?:\d+)(*MARK:major)|(?:\d+)(*MARK:minor)|(?:\d+)(*MARK:patch)|(?:(?:-[a-zA-Z0-9-]+))(*MARK:pre-release)|(?:(?:\+[a-zA-Z0-9-]+))(*MARK:build-metadata)|(?:\d+)(*MARK:number)|(?:[a-zA-Z0-9-]+)(*MARK:identifier))#Au',
            $grammar->compile()
        );
    }
}
