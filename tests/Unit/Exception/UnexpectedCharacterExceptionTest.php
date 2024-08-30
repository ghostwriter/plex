<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Plex\Exception\UnexpectedCharacterException;
use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Lexer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(UnexpectedCharacterException::class)]
#[UsesClass(Grammar::class)]
#[UsesClass(Lexer::class)]
final class UnexpectedCharacterExceptionTest extends TestCase
{
    public function testLexerException(): void
    {
        $grammar = Grammar::new([
            'DIGIT' => '\d+',
        ]);

        $lexer = Lexer::new($grammar);

        $this->expectException(UnexpectedCharacterException::class);
        $this->expectExceptionMessage('Unexpected character "#" on line 1 and column 0');

        iterator_to_array($lexer->lex('#BlackLivesMatter'));
    }
}
