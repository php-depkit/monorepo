<?php

declare(strict_types=1);

namespace PhpDepkit\Purl;

final readonly class Purl
{
    private string $type;

    private ?string $namespace;

    private string $name;

    private ?string $version;

    /**
     * @var array<string, string>
     */
    private array $qualifiers;

    private ?string $subpath;

    /**
     * @param array<string, string> $qualifiers
     */
    public function __construct(
        string $type,
        ?string $namespace,
        string $name,
        ?string $version = null,
        array $qualifiers = [],
        ?string $subpath = null,
    ) {
        $this->type = $type;
        $this->namespace = $this->normalizeOptional($namespace);
        $this->name = $name;
        $this->version = $this->normalizeOptional($version);
        $this->qualifiers = $this->normalizeQualifiers($qualifiers);
        $this->subpath = $this->normalizeSubpath($subpath);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function namespace(): ?string
    {
        return $this->namespace;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): ?string
    {
        return $this->version;
    }

    /**
     * @return array<string, string>
     */
    public function qualifiers(): array
    {
        return $this->qualifiers;
    }

    public function subpath(): ?string
    {
        return $this->subpath;
    }

    public function toString(): string
    {
        $purl = 'pkg:' . $this->type() . '/';

        if ($this->namespace() !== null) {
            $purl .= $this->encodeNamespace($this->namespace()) . '/';
        }

        $purl .= $this->encodeComponent($this->name());

        if ($this->version() !== null) {
            $purl .= '@' . $this->encodeComponent($this->version());
        }

        if ($this->qualifiers() !== []) {
            $pairs = [];

            foreach ($this->qualifiers() as $key => $value) {
                $pairs[] = $key . '=' . $this->encodeComponent($value);
            }

            $purl .= '?' . implode('&', $pairs);
        }

        if ($this->subpath() !== null) {
            $purl .= '#' . $this->subpath();
        }

        return $purl;
    }

    private function normalizeOptional(?string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    /**
     * @param array<string, string> $qualifiers
     *
     * @return array<string, string>
     */
    private function normalizeQualifiers(array $qualifiers): array
    {
        ksort($qualifiers);

        return $qualifiers;
    }

    private function normalizeSubpath(?string $subpath): ?string
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

    private function encodeNamespace(string $namespace): string
    {
        return implode(
            '/',
            array_map(fn (string $segment): string => $this->encodeComponent($segment), explode('/', $namespace)),
        );
    }

    private function encodeComponent(string $value): string
    {
        return str_ireplace(
            ['%3A', '%2C'],
            [':', ','],
            rawurlencode($value),
        );
    }
}
