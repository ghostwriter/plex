<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Interface;

interface BuilderInterface
{
    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     */
    public function add(string $reference, string $regularExpression): void;

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    public function alternation(string ...$regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function atLeast(int $min, string $regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function atMost(int $max, string $regularExpression): string;

    /**
     * @return non-empty-string
     */
    public function build(): string;

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    public function concatenation(string ...$regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function exactly(int $count, string $regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function group(string $regularExpression): string;

    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function mark(string $reference, string $regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function oneOrMore(string $regularExpression): string;

    /**
     * @param non-empty-string ...$regularExpression
     *
     * @return non-empty-string
     */
    public function option(string ...$regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function optional(string $regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function range(int $min, int $max, string $regularExpression): string;

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    public function reference(string $name): string;

    /**
     * @return array<non-empty-string,non-empty-string>
     */
    public function regularExpressions(): array;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function repetition(string $regularExpression): string;

    /**
     * @param non-empty-string $regularExpression
     *
     * @return non-empty-string
     */
    public function zeroOrMore(string $regularExpression): string;
}
