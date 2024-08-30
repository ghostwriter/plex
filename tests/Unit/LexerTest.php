<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Ghostwriter\Plex\Lexer;
use Ghostwriter\Plex\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

use function iterator_to_array;

#[CoversClass(Lexer::class)]
#[UsesClass(Grammar::class)]
#[UsesClass(Token::class)]
final class LexerTest extends AbstractTestCase
{
    #[DataProvider('provideGrammar')]
    public function testLex(GrammarInterface $grammar, array $expected, string $content): void
    {
        self::assertSameTokens($expected, iterator_to_array(Lexer::new($grammar)->lex($content)));
    }

    public function testLexFixture(): void
    {
        $grammar = Grammar::new([
            'T_NUMBER' => '\d+',
            'T_PLUS' => '\+',
            'T_MINUS' => '-',
            'T_WHITESPACE' => '\s+',
            'T_DOT' => '\.',
        ]);

        $content = '1.2.3';

        $expected = [
            Token::new('T_NUMBER', '1', 1, 1, []),
            Token::new('T_DOT', '.', 1, 2, []),
            Token::new('T_NUMBER', '2', 1, 3, []),
            Token::new('T_DOT', '.', 1, 4, []),
            Token::new('T_NUMBER', '3', 1, 5, []),
        ];

        self::assertSameTokens($expected, iterator_to_array(Lexer::new($grammar)->lex($content)));
    }
}
