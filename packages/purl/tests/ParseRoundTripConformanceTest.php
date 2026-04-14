<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Tests;

use PhpDepkit\Purl\Parser;
use PhpDepkit\Purl\Purl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ParseRoundTripConformanceTest extends TestCase
{
    #[DataProvider('parseCases')]
    public function test_it_parses_expected_components(string $input, array $expected): void
    {
        $purl = Parser::parse($input);

        self::assertInstanceOf(Purl::class, $purl);
        $this->assertPurlComponents($purl, $expected);
        self::assertSame($expected['canonical'], $purl->toString());
    }

    #[DataProvider('roundTripCases')]
    public function test_it_round_trips_to_canonical_output(string $input, string $canonical, array $expected): void
    {
        $parsed = Parser::parse($input);

        self::assertInstanceOf(Purl::class, $parsed);
        $this->assertPurlComponents($parsed, $expected);
        self::assertSame($canonical, $parsed->toString());

        $reparsed = Parser::parse($canonical);

        self::assertInstanceOf(Purl::class, $reparsed);
        $this->assertPurlComponents($reparsed, $expected);
        self::assertSame($canonical, $reparsed->toString());
    }

    /**
     * @return array<string, array{0: string, 1: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string, canonical: string}}>
     */
    public static function parseCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        return array_map(
            static fn (array $case): array => [$case['input'], $case['expected']],
            $cases['parse'],
        );
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string}}>
     */
    public static function roundTripCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        return array_map(
            static fn (array $case): array => [$case['input'], $case['canonical'], $case['expected']],
            $cases['roundtrip'],
        );
    }

    private function assertPurlComponents(Purl $purl, array $expected): void
    {
        self::assertSame($expected['type'], $purl->type());
        self::assertSame($expected['namespace'], $purl->namespace());
        self::assertSame($expected['name'], $purl->name());
        self::assertSame($expected['version'], $purl->version());
        self::assertSame($expected['qualifiers'], $purl->qualifiers());
        self::assertSame($expected['subpath'], $purl->subpath());
    }
}
