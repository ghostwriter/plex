<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Tests\Unit\Lexer\Token;

use Ghostwriter\Plex\Lexer\Token\Token;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Plex\Lexer\Token\Token
 *
 * @internal
 *
 * @small
 *
 */
final class TokenTest extends TestCase
{
    /**
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::__construct
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getFullStart
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getFullText
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getFullWidth
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getKind
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getStart
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getText
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getTriviaText
     * @covers \Ghostwriter\Plex\Lexer\Token\Token::getWidth
     */
    public function testToken(): void
    {
        $trivia = '  ';
        $bml = '#BlackLivesMatter';
        $content = sprintf('%s%s', $trivia, $bml);
        $token = new Token(0, 0, 2, mb_strlen($content));

        self::assertSame(0, $token->getKind());
        self::assertSame(2, $token->getStart());
        self::assertSame(17, $token->getWidth());
        self::assertSame(0, $token->getFullStart());
        self::assertSame(19, $token->getFullWidth());

        self::assertSame($bml, $token->getText($content));
        self::assertSame($content, $token->getFullText($content));
        self::assertSame($trivia, $token->getTriviaText($content));
    }
}
