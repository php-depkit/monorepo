<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Tests;

use PhpDepkit\Purl\Purl;
use PhpDepkit\Purl\PurlFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BuildConformanceTest extends TestCase
{
    public function test_it_normalizes_whitespace_only_optional_components(): void
    {
        $purl = PurlFactory::make(
            type: 'npm',
            namespace: '   ',
            name: 'core',
            version: "\t",
            qualifiers: [],
            subpath: '   ',
        );

        self::assertNull($purl->namespace());
        self::assertNull($purl->version());
        self::assertNull($purl->subpath());
        self::assertSame('pkg:npm/core', $purl->toString());
    }

    #[DataProvider('buildCases')]
    public function test_it_builds_canonical_purls_from_components(array $case): void
    {
        $purl = PurlFactory::make(
            type: $case['type'],
            namespace: $case['namespace'],
            name: $case['name'],
            version: $case['version'],
            qualifiers: $case['qualifiers'],
            subpath: $case['subpath'],
        );

        self::assertInstanceOf(Purl::class, $purl);
        self::assertSame($case['type'], $purl->type());
        self::assertSame($case['namespace'], $purl->namespace());
        self::assertSame($case['name'], $purl->name());
        self::assertSame($case['version'], $purl->version());
        self::assertSame(self::canonicalQualifiers($case['qualifiers']), $purl->qualifiers());
        self::assertSame(self::canonicalSubpath($case['subpath']), $purl->subpath());
        self::assertSame($case['expected'], $purl->toString());
    }

    /**
     * @return array<string, array{0: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string, expected: string}}>
     */
    public static function buildCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        return array_map(
            static fn (array $case): array => [$case],
            $cases['build'],
        );
    }

    /**
     * @param array<string, string> $qualifiers
     *
     * @return array<string, string>
     */
    private static function canonicalQualifiers(array $qualifiers): array
    {
        ksort($qualifiers);

        return $qualifiers;
    }

    private static function canonicalSubpath(?string $subpath): ?string
    {
        if ($subpath === null || $subpath === '') {
            return null;
        }

        $segments = [];

        foreach (explode('/', $subpath) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($segments);

                continue;
            }

            $segments[] = $segment;
        }

        if ($segments === []) {
            return null;
        }

        return implode('/', $segments);
    }
}
