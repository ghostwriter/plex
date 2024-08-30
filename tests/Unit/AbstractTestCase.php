<?php

declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Ghostwriter\Plex\Interface\TokenInterface;
use Ghostwriter\Plex\Token;
use Override;
use Throwable;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

use function json_encode;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @throws Throwable
     */
    final public static function assertSameTokens(array $expectedTokens, array $actualTokens): void
    {
        self::assertSame(
            json_encode($expectedTokens, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT),
            json_encode($actualTokens, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT),
        );
    }

    /**
     * @throws Throwable
     *
     * @return Generator<array{0:GrammarInterface,1:array<TokenInterface>,2:string}>
     *
     */
    final public static function provideGrammar(): Generator
    {
        foreach (self::provideRegularExpressions() as $name => [$regularExpressions, $expected, $content, $tokens]) {
            yield $name => [Grammar::new($regularExpressions), $tokens, $content];
        }
    }

    /**
     * @throws Throwable
     *
     * @return Generator<array{0:array<string,string>,1:string,2:array<TokenInterface>,3:string}>
     *
     */
    final public static function provideRegularExpressions(): Generator
    {
        // regexps, compiled, content, tokens
        yield from [
            'empty' => [[], '#(?|)#A', '', []],
            'single' => [
                [
                    'T_NUM' => '\d+',
                ],
                '#(?|(?:\d+)(*MARK:T_NUM))#A',
                '123',
                [Token::new('T_NUM', '123', 1, 3, [])],
            ],
            'multiple' => [
                [
                    'T_NUM' => '\d+',
                    'T_SPACE' => '\s+',
                    'T_STR' => '\w+',
                ],
                '#(?|(?:\d+)(*MARK:T_NUM)|(?:\s+)(*MARK:T_SPACE)|(?:\w+)(*MARK:T_STR))#A',
                '123 abc',
                [
                    Token::new('T_NUM', '123', 1, 3, []),
                    Token::new('T_SPACE', ' ', 1, 4, []),
                    Token::new('T_STR', 'abc', 1, 7, []),
                ],
            ],
            'direct-reference' => [
                [
                    'T_REF_NUM' => '(?&T_NUM)',      // Direct reference to T_NUM
                    'T_NUM' => '\d+',                // Matches numbers
                ],
                '#(?|(?:(?:\d+))(*MARK:T_REF_NUM)|(?:\d+)(*MARK:T_NUM))#A',
                '123',
                [Token::new('T_REF_NUM', '123', 1, 3, [])],
            ],
            'multiple-direct-reference' => [
                [
                    'T_ALL' => '(?&T_NUM)|(?&T_STR)', // Matches either numbers or word characters
                    'T_NUM' => '\d+',                // Matches numbers
                    'T_STR' => '\w+',                // Matches word characters
                    'T_SPACE' => '\s+',              // Matches spaces
                ],
                '#(?|(?:(?:\d+)|(?:\w+))(*MARK:T_ALL)|(?:\d+)(*MARK:T_NUM)|(?:\w+)(*MARK:T_STR)|(?:\s+)(*MARK:T_SPACE))#A',
                '123 abc',
                [
                    Token::new('T_ALL', '123', 1, 3, []),
                    Token::new('T_SPACE', ' ', 1, 4, []),
                    Token::new('T_ALL', 'abc', 1, 7, []),
                ],
            ],
            'nested-reference' => [
                [
                    'T_ALL' => '(?:(?&T_NUM)|(?&T_REF_STR))*',   // References both T_NUM and T_REF_STR (which references T_STR)
                    'T_NUM' => '\d+',                       // Matches numbers
                    'T_STR' => '\w+',                       // Matches word characters
                    'T_REF_STR' => '(?&T_STR)',             // References T_STR

                ],
                '#(?|(?:(?:(?:\d+)|(?:(?:\w+)))*)(*MARK:T_ALL)|(?:\d+)(*MARK:T_NUM)|(?:\w+)(*MARK:T_STR)|(?:(?:\w+))(*MARK:T_REF_STR))#A',
                '456def',
                [Token::new('T_ALL', '456def', 1, 6, [])],
            ],

            'readme' => [
                [
                    'T_SPACE' => '\s+',
                    'T_NUMBER' => '\d+',
                    'T_STRING' => '"[^"]*"',
                    'T_IDENTIFIER' => '[a-zA-Z_][a-zA-Z0-9_]*',
                    'T_OPERATOR' => '[\+\-\*\/]',
                    'T_PUNCTUATION' => '[\(\)\{\}\[\]\,]',
                ],
                '#(?|(?:\s+)(*MARK:T_SPACE)|(?:\d+)(*MARK:T_NUMBER)|(?:"[^"]*")(*MARK:T_STRING)|(?:[a-zA-Z_][a-zA-Z0-9_]*)(*MARK:T_IDENTIFIER)|(?:[\+\-\*\/])(*MARK:T_OPERATOR)|(?:[\(\)\{\}\[\]\,])(*MARK:T_PUNCTUATION))#A',
                '1 + 2 * (3 + 4)',
                [
                    Token::new('T_NUMBER', '1', 1, 1, []),
                    Token::new('T_SPACE', ' ', 1, 2, []),
                    Token::new('T_OPERATOR', '+', 1, 3, []),
                    Token::new('T_SPACE', ' ', 1, 4, []),
                    Token::new('T_NUMBER', '2', 1, 5, []),
                    Token::new('T_SPACE', ' ', 1, 6, []),
                    Token::new('T_OPERATOR', '*', 1, 7, []),
                    Token::new('T_SPACE', ' ', 1, 8, []),
                    Token::new('T_PUNCTUATION', '(', 1, 9, []),
                    Token::new('T_NUMBER', '3', 1, 10, []),
                    Token::new('T_SPACE', ' ', 1, 11, []),
                    Token::new('T_OPERATOR', '+', 1, 12, []),
                    Token::new('T_SPACE', ' ', 1, 13, []),
                    Token::new('T_NUMBER', '4', 1, 14, []),
                    Token::new('T_PUNCTUATION', ')', 1, 15, []),
                ],
            ],
        ];
    }
}
