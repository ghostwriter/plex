<?php

declare(strict_types=1);

namespace Ghostwriter\Plex;

use Ghostwriter\Plex\Interface\TokenInterface;
use Override;

final readonly class Token implements TokenInterface
{
    public function __construct(
        public string $type,
        public string $value,
        public int $line,
        public int $column,
        public array $matches,
    ) {
    }

    public function __toString(): string
    {
        return \sprintf('Token(%s, %s, %d, %d)', $this->type, $this->value, $this->line, $this->column);
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'line' => $this->line,
            'column' => $this->column,
            'matches' => $this->matches,
        ];
    }

    public static function new(string $type, string $value, int $line, int $column, array $matches = []): self
    {
        return new self($type, $value, $line, $column, $matches);
    }
}
