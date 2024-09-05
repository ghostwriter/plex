<?php

declare(strict_types=1);

namespace Ghostwriter\Plex;

use Ghostwriter\Plex\Exception\CircularReferenceException;
use Ghostwriter\Plex\Exception\ShouldNotHappenException;
use Ghostwriter\Plex\Exception\UndefinedReferenceException;
use Ghostwriter\Plex\Interface\BuilderInterface;
use Override;

final class Builder implements BuilderInterface
{
    /**
     * @var non-empty-string
     */
    private const string REFERENCE_REGULAR_EXPRESSION = '#\(\?&([\w-]+)\)#iu';

    /**
     * @var non-empty-string
     */
    private const string CIRCULAR_REFERENCE = CircularReferenceException::class;

    /**
     * @param array<non-empty-string,non-empty-string> $regularExpressions eg. ['num' => '\d','plus' => '\+','minus' => '\-'];
     */
    public function __construct(
        private array $regularExpressions,
    ) {
    }

    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     */
    #[Override]
    public function add(string $reference, string $regularExpression): void
    {
        $this->regularExpressions[$reference] = $regularExpression;
    }

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function alternation(string ...$regularExpression): string
    {
        /** @var non-empty-string */
        return \implode('|', $regularExpression);
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function atLeast(int $min, string $regularExpression): string
    {
        return \sprintf('%s%s', $this->option($regularExpression), $this->repetition(\sprintf('%d,', $min)));
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function atMost(int $max, string $regularExpression): string
    {
        return \sprintf('%s%s', $this->option($regularExpression), $this->repetition(\sprintf(',%d', $max)));
    }

    /**
     * @throws CircularReferenceException
     * @throws UndefinedReferenceException
     * @throws ShouldNotHappenException
     *
     * @return non-empty-string
     *
     */
    #[Override]
    public function build(): string
    {
        $regularExpressions = $this->regularExpressions;

        $regularExpression = \implode('|', \array_map(
            /**
             * @param non-empty-string $regularExpression
             * @param non-empty-string $mark
             *
             * @return non-empty-string
             */
            fn (string $regularExpression, string $mark): string => $this->mark($mark, $regularExpression),
            $regularExpressions,
            \array_keys($regularExpressions),
        ));

        try {
            $compiledRegularExpression = \preg_replace_callback(
                self::REFERENCE_REGULAR_EXPRESSION,
                /**
                 * @return non-empty-string
                 */
                fn (array $matches): string => /** @var array{0:non-empty-string,1:non-empty-string} $matches */
                $this->buildReference($matches[1]),
                $regularExpression
            );
        } catch (UndefinedReferenceException $exception) {
            throw new UndefinedReferenceException(
                \sprintf(
                    'Undefined reference "%s" detected in regular expression "%s"',
                    $exception->getMessage(),
                    $regularExpression
                ),
            );
        }

        if ($compiledRegularExpression === null) {
            throw new ShouldNotHappenException(\sprintf(
                'The regular expression "%s" could not be resolved',
                $regularExpression,
            ));
        }

        $this->regularExpressions = $regularExpressions;

        /** @return non-empty-string */
        return \sprintf('#(?|%s)#Au', \str_replace('#', '\#', $compiledRegularExpression));
    }

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function concatenation(string ...$regularExpression): string
    {
        /** @var non-empty-string */
        return \implode('', $regularExpression);
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function exactly(int $count, string $regularExpression): string
    {
        return \sprintf('%s%s', $this->option($regularExpression), $this->repetition(\sprintf('%d', $count)));
    }

    #[Override]
    public function group(string $regularExpression): string
    {
        return \sprintf('(?:%s)', $regularExpression);
    }

    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function mark(string $reference, string $regularExpression): string
    {
        return \sprintf('(?:%s)(*MARK:%s)', $regularExpression, $reference);
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function oneOrMore(string $regularExpression): string
    {
        return \sprintf('%s+', $this->group($regularExpression));
    }

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function option(string ...$regularExpression): string
    {
        return \sprintf('[%s]', $this->concatenation(...$regularExpression));
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function optional(string $regularExpression): string
    {
        return \sprintf('%s?', $this->group($regularExpression));
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function range(int $min, int $max, string $regularExpression): string
    {
        return \sprintf(
            '%s%s',
            $this->option($regularExpression),
            $this->repetition(\sprintf('%d,%d', $min, $max)),
        );
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    #[Override]
    public function reference(string $name): string
    {
        return \sprintf('(?&%s)', $name);
    }

    /**
     * @return array<non-empty-string,non-empty-string>
     */
    #[Override]
    public function regularExpressions(): array
    {
        return $this->regularExpressions;
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function repetition(string $regularExpression): string
    {
        return \sprintf('{%s}', $regularExpression);
    }

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    #[Override]
    public function zeroOrMore(string $regularExpression): string
    {
        return \sprintf('%s*', $this->group($regularExpression));
    }

    /**
     * @param non-empty-string $reference
     *
     * @throws CircularReferenceException
     * @throws UndefinedReferenceException
     *
     * @return non-empty-string
     */
    private function buildReference(string $reference): string
    {
        /**
         * @var list<non-empty-string> $references
         */
        static $references = [];

        $regularExpression = $this->regularExpressions[$reference] ?? throw new UndefinedReferenceException($reference);

        if (\in_array($reference, $references, true)) {
            throw new CircularReferenceException();
        }

        $references[] = $reference;

        /** @var non-empty-string $resolvedRegularExpression */
        $resolvedRegularExpression = \preg_replace_callback(
            self::REFERENCE_REGULAR_EXPRESSION,
            /**
             * @return non-empty-string
             */
            function (array $matches): string {
                /** @var array{0:non-empty-string,1:non-empty-string} $matches */
                try {
                    return $this->buildReference($matches[1]);
                } catch (CircularReferenceException) {
                    // Skip the circular reference and continue resolving other alternatives
                    return self::CIRCULAR_REFERENCE;
                }
            },
            $regularExpression
        ) ?? throw new ShouldNotHappenException(
            \sprintf(
                'The regular expression "%s" with reference "%s" could not be resolved',
                $regularExpression,
                $reference
            ),
        );

        \array_pop($references);

        if (\str_contains($resolvedRegularExpression, self::CIRCULAR_REFERENCE)) {
            throw new CircularReferenceException(\sprintf(
                'Circular reference detected for "%s" in regular expression "%s"',
                $reference,
                $regularExpression,
            ));
        }

        return $this->regularExpressions[$reference] = $resolvedRegularExpression;
    }

    /**
     * @param array<non-empty-string,non-empty-string> $regularExpressions eg. ['num' => '\d','plus' => '\+','minus' => '\-'];
     */
    public static function new(array $regularExpressions = []): self
    {
        return new self($regularExpressions);
    }
}
