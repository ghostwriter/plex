<?php

declare(strict_types=1);

namespace Ghostwriter\Plex;

use Generator;
use Ghostwriter\Plex\Exception\ShouldNotHappenException;
use Ghostwriter\Plex\Exception\UnexpectedCharacterException;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Ghostwriter\Plex\Interface\LexerInterface;
use Ghostwriter\Plex\Interface\TokenInterface;
use Override;

use function mb_strlen;
use function mb_substr_count;
use function preg_match;
use function sprintf;

final readonly class Lexer implements LexerInterface
{
    public function __construct(
        private GrammarInterface $grammar,
    ) {
    }

    /**
     * @return Generator<TokenInterface>
     */
    #[Override]
    public function lex(string $content): Generator
    {
        $column = 0;
        $line = 1;

        $regularExpression = $this->grammar->compile();

        while (isset($content[$column])) {
            if (! preg_match($regularExpression, $content, $matches, 0, $column)) {
                throw new UnexpectedCharacterException(
                    sprintf(
                        'Unexpected character "%s" on line %d and column %d',
                        $content[$column],
                        $line,
                        $column,
                    ),
                );
            }

            /** @var array{'MARK':non-empty-string,0:non-empty-string} $matches */
            $lineNumber = $line;

            $text = $matches[0] ?? throw new ShouldNotHappenException(
                'The matches array should always have a 0 index',
            );

            $line += mb_substr_count($text, "\n");
            $column += mb_strlen($text);

            $name = $matches['MARK'] ?? throw new ShouldNotHappenException(
                'The matches array should always have a MARK index',
            );

            unset($matches[0], $matches['MARK']);

            yield Token::new($name, $text, $lineNumber, $column, $matches);
        }
    }

    public static function new(GrammarInterface $grammar): self
    {
        return new self($grammar);
    }
}
