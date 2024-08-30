<?php

declare(strict_types=1);

namespace Ghostwriter\Plex\Interface;

interface GrammarInterface
{
    /**
     * @return non-empty-string
     */
    public function compile(): string;
}
