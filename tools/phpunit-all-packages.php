<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$phpunitBin = $rootDir . '/vendor/bin/phpunit';

if (! is_file($phpunitBin)) {
    fwrite(STDERR, "phpunit binary not found. Run 'composer install' first.\n");
    exit(1);
}

$packageDirs = glob($rootDir . '/packages/*', GLOB_ONLYDIR);
if ($packageDirs === false) {
    fwrite(STDERR, "Failed to scan package directories.\n");
    exit(1);
}

sort($packageDirs);

$configs = [];
foreach ($packageDirs as $packageDir) {
    $phpunitXml = $packageDir . '/phpunit.xml';
    $phpunitXmlDist = $packageDir . '/phpunit.xml.dist';

    if (is_file($phpunitXml)) {
        $configs[] = $phpunitXml;
        continue;
    }

    if (is_file($phpunitXmlDist)) {
        $configs[] = $phpunitXmlDist;
    }
}

if ($configs === []) {
    fwrite(STDOUT, "No package-level PHPUnit configuration files found.\n");
    exit(0);
}

$failedConfigs = [];

foreach ($configs as $config) {
    fwrite(STDOUT, sprintf("\n==> Running PHPUnit for %s\n", substr($config, strlen($rootDir) + 1)));

    $command = sprintf(
        '%s --configuration %s',
        escapeshellarg($phpunitBin),
        escapeshellarg($config)
    );

    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        $failedConfigs[] = $config;
    }
}

if ($failedConfigs !== []) {
    fwrite(STDERR, "\nPHPUnit failed for:\n");
    foreach ($failedConfigs as $failedConfig) {
        fwrite(STDERR, '- ' . substr($failedConfig, strlen($rootDir) + 1) . "\n");
    }

    exit(1);
}

fwrite(STDOUT, "\nAll package PHPUnit suites passed.\n");
