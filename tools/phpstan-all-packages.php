<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$phpstanBin = $rootDir . '/vendor/bin/phpstan';

if (! is_file($phpstanBin)) {
    fwrite(STDERR, "phpstan binary not found. Run 'composer install' first.\n");
    exit(1);
}

$packageDirs = glob($rootDir . '/packages/*', GLOB_ONLYDIR);
if ($packageDirs === false) {
    fwrite(STDERR, "Failed to scan package directories.\n");
    exit(1);
}

sort($packageDirs);

$paths = [];
foreach ($packageDirs as $packageDir) {
    $candidatePath = $packageDir . '/src';
    if (is_dir($candidatePath)) {
        $paths[] = $candidatePath;
    }
}

if ($paths === []) {
    fwrite(STDOUT, "No package src directories found for PHPStan analysis.\n");
    exit(0);
}

$escapedPaths = array_map(static fn (string $path): string => escapeshellarg($path), $paths);

$command = sprintf(
    '%s analyse --configuration %s %s',
    escapeshellarg($phpstanBin),
    escapeshellarg($rootDir . '/phpstan.neon.dist'),
    implode(' ', $escapedPaths)
);

fwrite(STDOUT, "Running PHPStan at level 9 across package directories.\n\n");
passthru($command, $exitCode);

exit($exitCode);
