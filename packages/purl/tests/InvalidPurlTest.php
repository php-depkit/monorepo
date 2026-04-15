<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Tests;

use PhpDepkit\Purl\Exception\Build\InvalidQualifierKey;
use PhpDepkit\Purl\Exception\Build\MissingRequiredComponent;
use PhpDepkit\Purl\Exception\InvalidPurl;
use PhpDepkit\Purl\Parser;
use PhpDepkit\Purl\PurlFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InvalidPurlTest extends TestCase
{
    #[DataProvider('invalidParseCases')]
    public function testItRejectsInvalidParseInputs(string $input, array $expectedContext): void
    {
        try {
            Parser::parse($input);

            self::fail('Expected parse input to be rejected.');
        } catch (InvalidPurl $exception) {
            self::assertSame('Invalid package URL.', $exception->getMessage());
            self::assertSame($expectedContext, $exception->context());
            self::assertStringNotContainsString($input, $exception->getMessage());
        }
    }

    #[DataProvider('invalidBuildCases')]
    public function testItRejectsInvalidBuildInputs(array $case, array $expectation): void
    {
        try {
            PurlFactory::make(
                type: $case['type'],
                namespace: $case['namespace'],
                name: $case['name'],
                version: $case['version'],
                qualifiers: $case['qualifiers'],
                subpath: $case['subpath'],
            );

            self::fail('Expected build input to be rejected.');
        } catch (InvalidPurl $exception) {
            self::assertInstanceOf($expectation['class'], $exception);
            self::assertSame($expectation['message'], $exception->getMessage());
            self::assertSame($expectation['context'], $exception->context());

            foreach (self::rejectedRawValues($case) as $value) {
                self::assertStringNotContainsString($value, $exception->getMessage());
            }
        }
    }

    /**
     * @return array<string, array{0: string, 1: array{component: string, reason: string}}>
     */
    public static function invalidParseCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        return array_map(
            static fn (array $case): array => [
                $case['input'],
                [
                    'component' => $case['component'],
                    'reason' => $case['reason'],
                ],
            ],
            $cases['invalid']['parse'],
        );
    }

    /**
     * @return array<string, array{0: array{type: string, namespace: ?string, name: string, version: ?string, qualifiers: array<string, string>, subpath: ?string, reason: string}, 1: array{class: class-string<InvalidPurl>, message: string, context: array{component: string, reason: string}}}>
     */
    public static function invalidBuildCases(): array
    {
        $cases = require __DIR__ . '/Fixtures/ConformanceCases.php';

        $expectations = [
            'missing type' => [
                'class' => MissingRequiredComponent::class,
                'message' => 'Missing required package URL component.',
                'context' => [
                    'component' => 'type',
                    'reason' => 'missing',
                ],
            ],
            'missing name' => [
                'class' => MissingRequiredComponent::class,
                'message' => 'Missing required package URL component.',
                'context' => [
                    'component' => 'name',
                    'reason' => 'missing',
                ],
            ],
            'invalid qualifier key' => [
                'class' => InvalidQualifierKey::class,
                'message' => 'Invalid package URL qualifier.',
                'context' => [
                    'component' => 'qualifiers',
                    'reason' => 'invalid_key',
                ],
            ],
        ];

        return array_map(
            static fn (array $case): array => [$case, $expectations[$case['reason']]],
            $cases['invalid']['build'],
        );
    }

    /**
     * @return list<string>
     */
    private static function rejectedRawValues(array $case): array
    {
        $values = [];

        foreach (['type', 'namespace', 'name', 'version', 'subpath'] as $key) {
            $value = $case[$key];

            if (is_string($value) && $value !== '') {
                $values[] = $value;
            }
        }

        foreach ($case['qualifiers'] as $key => $value) {
            $values[] = $key;

            if ($value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }
}
