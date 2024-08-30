<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Interface;

use Generator;

interface LexerInterface
{
    /**
     * @return Generator<TokenInterface>
     */
    public function lex(string $content): Generator;
}
