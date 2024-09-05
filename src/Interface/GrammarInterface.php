<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Interface;

interface GrammarInterface
{
    /**
     * @return non-empty-string
     */
    public function compile(): string;

    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     */
    public function define(string $reference, string $regularExpression): void;
}
