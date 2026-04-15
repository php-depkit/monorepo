<?php

declare(strict_types=1);

namespace PhpDepkit\Purl;

use PhpDepkit\Purl\Exception\InvalidPurl;

final class Parser
{
    private const string QUALIFIER_KEY_PATTERN = '/\A[a-zA-Z0-9._-]+\z/';

    public static function parse(string $purl): Purl
    {
        if (! str_starts_with($purl, 'pkg:')) {
            throw self::invalid(component: 'scheme', reason: 'missing');
        }

        $remainder = substr($purl, 4);
        $subpath = self::extractSubpath($remainder);
        $qualifiers = self::extractQualifiers($remainder);
        $version = self::extractVersion($remainder);

        [$type, $namespace, $name] = self::extractPathComponents($remainder);

        return PurlFactory::make(
            type: $type,
            namespace: $namespace,
            name: $name,
            version: $version,
            qualifiers: $qualifiers,
            subpath: $subpath,
        );
    }

    private static function extractSubpath(string &$remainder): ?string
    {
        $position = strpos($remainder, '#');

        if ($position === false) {
            return null;
        }

        $subpath = substr($remainder, $position + 1);
        $remainder = substr($remainder, 0, $position);

        if ($subpath === '') {
            return null;
        }

        $segments = [];

        foreach (explode('/', $subpath) as $segment) {
            $segments[] = self::decode(component: 'subpath', value: $segment);
        }

        return implode('/', $segments);
    }

    /**
     * @return array<string, string>
     */
    private static function extractQualifiers(string &$remainder): array
    {
        $position = strpos($remainder, '?');

        if ($position === false) {
            return [];
        }

        $query = substr($remainder, $position + 1);
        $remainder = substr($remainder, 0, $position);

        if ($query === '') {
            return [];
        }

        $qualifiers = [];

        foreach (explode('&', $query) as $pair) {
            if ($pair === '') {
                throw self::invalid(component: 'qualifiers', reason: 'invalid');
            }

            $separator = strpos($pair, '=');

            if ($separator === false) {
                throw self::invalid(component: 'qualifiers', reason: 'invalid');
            }

            $rawKey = substr($pair, 0, $separator);
            $rawValue = substr($pair, $separator + 1);
            $key = self::decode(component: 'qualifiers', value: $rawKey);

            if ($key === '' || preg_match(self::QUALIFIER_KEY_PATTERN, $key) !== 1) {
                throw self::invalid(component: 'qualifiers', reason: 'invalid_key');
            }

            $qualifiers[$key] = self::decode(component: 'qualifiers', value: $rawValue);
        }

        return $qualifiers;
    }

    private static function extractVersion(string &$remainder): ?string
    {
        $position = strrpos($remainder, '@');

        if ($position === false) {
            return null;
        }

        $version = substr($remainder, $position + 1);
        $remainder = substr($remainder, 0, $position);

        if ($version === '') {
            return null;
        }

        return self::decode(component: 'version', value: $version);
    }

    /**
     * @return array{0: string, 1: ?string, 2: string}
     */
    private static function extractPathComponents(string $remainder): array
    {
        $separator = strpos($remainder, '/');

        if ($separator === false) {
            $type = $remainder;
            $path = '';
        } else {
            $type = substr($remainder, 0, $separator);
            $path = substr($remainder, $separator + 1);
        }

        if ($type === '') {
            throw self::invalid(component: 'type', reason: 'missing');
        }

        if ($path === '') {
            throw self::invalid(component: 'name', reason: 'missing');
        }

        $segments = explode('/', $path);
        $rawName = array_pop($segments);

        if ($rawName === '') {
            throw self::invalid(component: 'name', reason: 'missing');
        }

        $name = self::decode(component: 'name', value: $rawName);
        $namespace = null;

        if ($segments !== []) {
            $decodedSegments = [];

            foreach ($segments as $segment) {
                if ($segment === '') {
                    throw self::invalid(component: 'namespace', reason: 'invalid');
                }

                $decodedSegments[] = self::decode(component: 'namespace', value: $segment);
            }

            $namespace = implode('/', $decodedSegments);
        }

        return [$type, $namespace, $name];
    }

    private static function decode(string $component, string $value): string
    {
        if (preg_match('/%(?![0-9A-F]{2})/', $value) === 1) {
            throw self::invalid(component: $component, reason: 'invalid_encoding');
        }

        return rawurldecode($value);
    }

    private static function invalid(string $component, string $reason): InvalidPurl
    {
        return new InvalidPurl('Invalid package URL.', [
            'component' => $component,
            'reason' => $reason,
        ]);
    }
}
