<?php

declare(strict_types=1);

namespace Ghostwriter\Plex;

use Ghostwriter\Plex\Interface\BuilderInterface;
use Ghostwriter\Plex\Interface\GrammarInterface;
use Override;

final readonly class Grammar implements GrammarInterface
{
    public function __construct(
        private BuilderInterface $builder
    ) {
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function compile(): string
    {
        return $this->builder->build();
    }

    /**
     * @param non-empty-string $reference
     * @param non-empty-string $regularExpression
     */
    #[Override]
    public function define(string $reference, string $regularExpression): void
    {
        $this->builder->add($reference, $regularExpression);
    }

    /**
     * @param array<non-empty-string,non-empty-string> $regularExpressions
     */
    public static function new(array $regularExpressions): self
    {
        return new self(Builder::new($regularExpressions));
    }
}
