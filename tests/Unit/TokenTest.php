<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Plex\Interface\TokenInterface;
use Ghostwriter\Plex\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use Throwable;

use const JSON_THROW_ON_ERROR;

use function json_encode;

#[CoversClass(Token::class)]
final class TokenTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testConstruct(): void
    {
        $expectedToken = new Token('type', 'value', 1, 1, ['matches']);

        $actualToken = Token::new('type', 'value', 1, 1, ['matches']);

        self::assertSame((string) $expectedToken, (string) $actualToken);

        self::assertSame(
            json_encode($expectedToken, JSON_THROW_ON_ERROR),
            json_encode($actualToken, JSON_THROW_ON_ERROR)
        );

        self::assertInstanceof(TokenInterface::class, $actualToken);
        self::assertInstanceof(TokenInterface::class, $expectedToken);
    }
}
