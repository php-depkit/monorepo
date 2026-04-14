# purl

`php-depkit/purl` parses raw package URLs, builds canonical `Purl` objects, and renders stable package URL strings.

## Quick start

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\Parser;
use PhpDepkit\Purl\PurlFactory;

$parsed = Parser::parse('pkg:npm/%40angular/animation@12.3.1');

echo $parsed->type();      // npm
echo $parsed->namespace(); // @angular
echo $parsed->name();      // animation
echo $parsed->toString();  // pkg:npm/%40angular/animation@12.3.1

$built = PurlFactory::make(
    type: 'composer',
    namespace: 'guzzlehttp',
    name: 'promises',
    version: '2.0.2',
);

echo $built->toString();   // pkg:composer/guzzlehttp/promises@2.0.2
```

## Parse, build, and stringify

Use `Parser::parse()` when you receive a raw package URL string. Use `PurlFactory::make()` when you already have separated components. In both cases, call `toString()` on the returned `Purl` to render the canonical form.

### Parse a raw package URL

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\Parser;

$purl = Parser::parse(
    'pkg:npm/%40angular/animation@12.3.1'
);

echo $purl->type();      // npm
echo $purl->namespace(); // @angular
echo $purl->name();      // animation
echo $purl->version();   // 12.3.1
echo $purl->toString();  // pkg:npm/%40angular/animation@12.3.1
```

### Build from separated components

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\PurlFactory;

$purl = PurlFactory::make(
    type: 'composer',
    namespace: 'guzzlehttp',
    name: 'promises',
    version: '2.0.2',
);

echo $purl->toString();
// pkg:composer/guzzlehttp/promises@2.0.2
```

### Canonicalize valid but non-canonical input

`Parser::parse()` accepts structurally valid input and normalizes it through `toString()`.

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\Parser;

$purl = Parser::parse(
    'pkg:maven/org.springframework/spring-core@5.3.30?type=jar&repository_url=https:%2F%2Frepo.spring.io%2Frelease#src/./docs//../guides/.//install'
);

echo $purl->toString();
// pkg:maven/org.springframework/spring-core@5.3.30?repository_url=https:%2F%2Frepo.spring.io%2Frelease&type=jar#src/guides/install
```

That example keeps the package valid, but `toString()` sorts qualifier keys and cleans the subpath.

### Qualifier-bearing example

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\PurlFactory;

$purl = PurlFactory::make(
    type: 'generic',
    namespace: null,
    name: 'artifact',
    version: '1.0.0',
    qualifiers: [
        'download_url' => 'https://example.com/archive.tgz',
        'checksum' => 'sha256:abc123,sha512:def456',
    ],
);

echo $purl->toString();
// pkg:generic/artifact@1.0.0?checksum=sha256:abc123,sha512:def456&download_url=https:%2F%2Fexample.com%2Farchive.tgz
```

## Public API

### `Parser::parse(string $purl): Purl`

Parses a raw package URL string and returns a canonical `Purl` value object.

### `PurlFactory::make(...)`

Builds a `Purl` from separated components:

```php
PurlFactory::make(
    string $type,
    ?string $namespace,
    string $name,
    ?string $version = null,
    array $qualifiers = [],
    ?string $subpath = null,
): Purl
```

### `Purl` getters and `toString()`

The returned object exposes:

- `type()`
- `namespace()`
- `name()`
- `version()`
- `qualifiers()`
- `subpath()`
- `toString()`

## Invalid input handling

Parse failures raise `InvalidPurl`. Keep the message generic in your own error handling and read `context()` when you need the stable failure details.

```php
<?php

declare(strict_types=1);

use PhpDepkit\Purl\Exception\InvalidPurl;
use PhpDepkit\Purl\Parser;

try {
    Parser::parse('pkg:generic/artifact?in production=true');
} catch (InvalidPurl $exception) {
    echo $exception->getMessage(); // Invalid package URL.

    $context = $exception->context();

    echo $context['component']; // qualifiers
    echo $context['reason'];    // invalid_key
}
```

Supported parse context keys:

- `component`
- `reason`
