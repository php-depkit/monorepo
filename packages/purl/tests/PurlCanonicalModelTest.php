<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Tests;

use PhpDepkit\Purl\Purl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PurlCanonicalModelTest extends TestCase
{
    public function testItIsAReadonlyValueObject(): void
    {
        $reflection = new ReflectionClass(Purl::class);

        self::assertTrue($reflection->isReadOnly());
        self::assertTrue($reflection->isFinal());
    }

    #[DataProvider('canonicalModelCases')]
    public function testItExposesCanonicalComponentsAndRendering(array $input, array $expected): void
    {
        $purl = new Purl(
            type: $input['type'],
            namespace: $input['namespace'],
            name: $input['name'],
            version: $input['version'],
            qualifiers: $input['qualifiers'],
            subpath: $input['subpath'],
        );

        self::assertSame($expected['type'], $purl->type());
        self::assertSame($expected['namespace'], $purl->namespace());
        self::assertSame($expected['name'], $purl->name());
        self::assertSame($expected['version'], $purl->version());
        self::assertSame($expected['qualifiers'], $purl->qualifiers());
        self::assertSame($expected['subpath'], $purl->subpath());
        self::assertSame($expected['canonical'], $purl->toString());
    }

    /**
     * @return array<string, array{0: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string}, 1: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string, canonical: string}}>
     */
    public static function canonicalModelCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        return [
            'simple canonical components stay visible in memory' => [
                $cases['build']['composer package keeps simple canonical form'],
                [
                    'type' => $cases['build']['composer package keeps simple canonical form']['type'],
                    'namespace' => $cases['build']['composer package keeps simple canonical form']['namespace'],
                    'name' => $cases['build']['composer package keeps simple canonical form']['name'],
                    'version' => $cases['build']['composer package keeps simple canonical form']['version'],
                    'qualifiers' => $cases['build']['composer package keeps simple canonical form']['qualifiers'],
                    'subpath' => $cases['build']['composer package keeps simple canonical form']['subpath'],
                    'canonical' => $cases['build']['composer package keeps simple canonical form']['expected'],
                ],
            ],
            'qualifiers are sorted in memory and rendering' => [
                $cases['build']['maven qualifiers are sorted and encoded'],
                [
                    'type' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['type'],
                    'namespace' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['namespace'],
                    'name' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['name'],
                    'version' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['version'],
                    'qualifiers' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['qualifiers'],
                    'subpath' => $cases['parse']['maven package preserves qualifiers and encoded repository url']['expected']['subpath'],
                    'canonical' => $cases['build']['maven qualifiers are sorted and encoded']['expected'],
                ],
            ],
            'subpath cleanup is canonical in memory and output' => [
                $cases['build']['subpath cleanup removes dot segments and empties'],
                [
                    'type' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['type'],
                    'namespace' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['namespace'],
                    'name' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['name'],
                    'version' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['version'],
                    'qualifiers' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['qualifiers'],
                    'subpath' => $cases['parse']['subpath cleanup canonicalizes traversals']['expected']['subpath'],
                    'canonical' => $cases['build']['subpath cleanup removes dot segments and empties']['expected'],
                ],
            ],
        ];
    }
}
