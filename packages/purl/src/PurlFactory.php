<?php

declare(strict_types=1);

namespace PhpDepkit\Purl;

use PhpDepkit\Purl\Exception\Build\InvalidQualifierKey;
use PhpDepkit\Purl\Exception\Build\MissingRequiredComponent;

final class PurlFactory
{
    private const string QUALIFIER_KEY_PATTERN = '/\A[a-zA-Z0-9._-]+\z/';

    /**
     * @param array<string, string> $qualifiers
     */
    public static function make(
        string $type,
        ?string $namespace,
        string $name,
        ?string $version = null,
        array $qualifiers = [],
        ?string $subpath = null,
    ): Purl {
        self::assertRequiredComponent(component: 'type', value: $type);
        self::assertRequiredComponent(component: 'name', value: $name);
        self::assertQualifierKeys($qualifiers);

        return new Purl(
            type: $type,
            namespace: self::normalizeOptional($namespace),
            name: $name,
            version: self::normalizeOptional($version),
            qualifiers: $qualifiers,
            subpath: self::normalizeOptional($subpath),
        );
    }

    private static function assertRequiredComponent(string $component, string $value): void
    {
        if (trim($value) === '') {
            throw MissingRequiredComponent::for($component);
        }
    }

    /**
     * @param array<string, string> $qualifiers
     */
    private static function assertQualifierKeys(array $qualifiers): void
    {
        foreach ($qualifiers as $key => $_value) {
            if ($key === '' || preg_match(self::QUALIFIER_KEY_PATTERN, $key) !== 1) {
                throw InvalidQualifierKey::forBuild();
            }
        }
    }

    private static function normalizeOptional(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return $value;
    }
}
