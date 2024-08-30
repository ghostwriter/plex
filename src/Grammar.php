<?php

declare(strict_types=1);

namespace Ghostwriter\Plex;

use Ghostwriter\Plex\Exception\ShouldNotHappenException;
use Ghostwriter\Plex\Exception\UndefinedReferenceException;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Override;

use function array_key_exists;
use function array_keys;
use function array_map;
use function implode;
use function preg_replace_callback;
use function sprintf;
use function str_replace;

final readonly class Grammar implements GrammarInterface
{
    public const string REFERENCE_REGULAR_EXPRESSION = '#\(\?&(\w+)\)#';

    /**
     * eg. [
     *   'T_NUMBER' => '\d+',
     *   'T_PLUS' => '\+',
     *   'T_MINUS' => '-'
     * ];.
     *
     * @param array<non-empty-string,non-empty-string> $regularExpressions
     */
    public function __construct(
        private array $regularExpressions,
    ) {
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function compile(): string
    {
        return self::resolveReference(
            sprintf('#(?|%s)#A', str_replace(
                '#',
                '\#',
                implode('|', array_map(
                    /**
                     * @return non-empty-string
                     */
                    static fn (
                        string $regularExpression,
                        string $mark
                    ): string => sprintf('(?:%s)(*MARK:%s)', $regularExpression, $mark),
                    $this->regularExpressions,
                    array_keys($this->regularExpressions),
                )),
            )),
            $this->regularExpressions
        );
    }

    public static function new(array $regularExpressions): self
    {
        return new self($regularExpressions);
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    private static function resolveReference(string $regularExpression, array $regularExpressions): string
    {
        return preg_replace_callback(self::REFERENCE_REGULAR_EXPRESSION, static function (array $matches) use (
            &$regularExpressions
        ): string {
            $token = $matches[1] ?? throw new ShouldNotHappenException(
                'The reference token should always be present'
            );

            if (array_key_exists($token, $regularExpressions)) {
                // Recursively resolve the pattern for the referenced token
                return '(?:' . self::resolveReference($regularExpressions[$token], $regularExpressions) . ')';
            }

            throw new UndefinedReferenceException(sprintf(
                'Undefined reference "%s" in regular expression "%s"',
                $token,
                $matches[0]
            ));
        }, $regularExpression);
    }
}
