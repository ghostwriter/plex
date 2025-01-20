<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Interface\TokenInterface;
use Ghostwriter\Plex\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use Throwable;

use const JSON_THROW_ON_ERROR;

#[CoversClass(Token::class)]
final class TokenTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testTokenInstanceOfTokenInterface(): void
    {
        $token = Token::new('type', 'value', 1, 1, ['matches']);

        self::assertInstanceof(TokenInterface::class, $token);
    }

    /**
     * @throws Throwable
     */
    public function testTokenJsonSerialize(): void
    {
        $expectedToken = new Token('type', 'value', 1, 1, ['matches']);

        $actualToken = Token::new('type', 'value', 1, 1, ['matches']);

        self::assertSame(
            \json_encode($expectedToken, JSON_THROW_ON_ERROR),
            \json_encode($actualToken, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws Throwable
     */
    public function testTokenToString(): void
    {
        $expectedToken = new Token('type', 'value', 1, 1, ['matches']);

        $actualToken = Token::new('type', 'value', 1, 1, ['matches']);

        self::assertSame((string) $expectedToken, (string) $actualToken);
    }
}
