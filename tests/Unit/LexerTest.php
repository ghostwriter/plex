<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Builder;
use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Ghostwriter\Plex\Lexer;
use Ghostwriter\Plex\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(Lexer::class)]
#[UsesClass(Builder::class)]
#[UsesClass(Grammar::class)]
#[UsesClass(Token::class)]
final class LexerTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    #[DataProvider('provideGrammar')]
    public function testLexerLex(GrammarInterface $grammar, array $expected, string $content): void
    {
        self::assertSameTokens($expected, \iterator_to_array(Lexer::new($grammar)->lex($content)));
    }
}
