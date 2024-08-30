#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Ghostwriter\Wip\Console;

use ErrorException;
use Ghostwriter\Wip\Foo;

use const DIRECTORY_SEPARATOR;
use const STDERR;

use function dirname;
use function file_exists;
use function fwrite;
use function set_error_handler;

/** @var ?string $_composer_autoload_path */
(static function (string $autoloader): void {
    set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 255, $severity, $file, $line);
    });

    if (! file_exists($autoloader)) {
        $message = '[ERROR]Cannot locate "' . $autoloader . '"\n please run "composer install"\n';
        fwrite(STDERR, $message);
        exit;
    }

    require $autoloader;

    /**
     * #BlackLivesMatter.
     */
    echo Foo::new()->test();
})($_composer_autoload_path ?? dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php');
