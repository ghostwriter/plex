<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Plex\Builder;
use Ghostwriter\Plex\Exception\CircularReferenceException;
use Ghostwriter\Plex\Grammar;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Throwable;

#[CoversClass(CircularReferenceException::class)]
#[UsesClass(Builder::class)]
#[UsesClass(Grammar::class)]
final class CircularReferenceExceptionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testBuilderThrowCircularReferenceException(): void
    {
        $this->expectException(CircularReferenceException::class);
        $this->expectExceptionMessage('Circular reference detected for "B" in regular expression "(?&A)"');

        $builder = Builder::new();

        $builder->add('A', '(?&B)|(?&C)');
        $builder->add('B', '(?&A)');
        $builder->add('C', '(?&A)');

        $builder->build();
    }

    /**
     * @throws Throwable
     */
    public function testGrammarThrowCircularReferenceException(): void
    {
        $this->expectException(CircularReferenceException::class);
        $this->expectExceptionMessage('Circular reference detected for "B" in regular expression "(?&A)"');

        $grammar = Grammar::new([
            'A' => '(?&B)|(?&C)',
            'B' => '(?&A)',
            'C' => '(?&A)',
        ]);

        $grammar->compile();
    }
}
