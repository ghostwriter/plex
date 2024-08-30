<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Plex\Exception\ShouldNotHappenException;
use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Lexer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(ShouldNotHappenException::class)]
#[UsesClass(Grammar::class)]
#[UsesClass(Lexer::class)]
final class ShouldNotHappenExceptionTest extends TestCase
{
    public function testLexerException(): void
    {
        $grammar = Grammar::new([/* no rules */]);

        $lexer = Lexer::new($grammar);

        $this->expectException(ShouldNotHappenException::class);
        $this->expectExceptionMessage('The matches array should always have a MARK index');

        iterator_to_array($lexer->lex('#BlackLivesMatter'));
    }
}
