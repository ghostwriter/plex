# Plex

[![Compliance](https://github.com/ghostwriter/plex/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/plex/actions/workflows/compliance.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/plex?color=8892bf)](https://www.php.net/supported-versions)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/plex&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Code Coverage](https://codecov.io/gh/ghostwriter/plex/branch/main/graph/badge.svg)](https://codecov.io/gh/ghostwriter/plex)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/plex/coverage.svg)](https://shepherd.dev/github/ghostwriter/plex)
[![Psalm Level](https://shepherd.dev/github/ghostwriter/plex/level.svg)](https://psalm.dev/docs/running_psalm/error_levels)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/plex)](https://packagist.org/packages/ghostwriter/plex)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/plex?color=blue)](https://packagist.org/packages/ghostwriter/plex)

Provides the fastest lexer for PHP, tokenizing text with named regex patterns for efficient processing.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/plex
```

### Star ⭐️ this repo if you find it useful

You can also star (🌟) this repo to find it easier later.

## Usage

```php
<?php

declare(strict_types=1);

use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Token;
use Ghostwriter\Plex\Lexer;


$grammar = Grammar::new([
    'T_NUMBER' => '\d+',
    'T_DOT' => '\.'
]);

$lexer = \Ghostwriter\Plex\Lexer::new($grammar);

$expected = [
    Token::new('T_NUMBER', '1', 1, 1, []),
    Token::new('T_DOT', '.', 1, 2, []),
    Token::new('T_NUMBER', '2', 1, 3, []),
    Token::new('T_DOT', '.', 1, 4, []),
    Token::new('T_NUMBER', '3', 1, 5, []),
];

$content = '1.2.3';

assert($expected == iterator_to_array($lexer->lex($content)));
```

### Named Patterns

When defining a grammar, you can reference other grammar rules by name using the `(?&NAME)` syntax, where `NAME` is the name of the grammar rule.

This allows you to create complex patterns that are easier to read and maintain.

``` php
<?php

declare(strict_types=1);

use Ghostwriter\Plex\Grammar;
use Ghostwriter\Plex\Token;
use Ghostwriter\Plex\Lexer;


$grammar = Grammar::new([
    'T_ID' => '(?:(?&T_NUM)|(?&T_REF_STR))*',   // References both T_NUM and T_REF_STR (which references T_STR)
    'T_NUM' => '\d+',                       // Matches numbers
    'T_STR' => '\w+',                       // Matches word characters
    'T_REF_STR' => '(?&T_STR)',             // References T_STR
]);

$lexer = \Ghostwriter\Plex\Lexer::new($grammar);

$expected = [
    Token::new('T_ID', '456def', 1, 6, [])
];

$content = '456def';

assert($expected == iterator_to_array($lexer->lex($content)));
```

### Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/plex/contributors)

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information on what has changed recently.

### License

Please see [LICENSE](./LICENSE) for more information on the license that applies to this project.

### Security

Please see [SECURITY.md](./SECURITY.md) for more information on security disclosure process.
