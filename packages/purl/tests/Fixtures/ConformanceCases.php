<?php

declare(strict_types=1);

return [
    'build' => [
        'composer package keeps simple canonical form' => [
            'type' => 'composer',
            'namespace' => 'guzzlehttp',
            'name' => 'promises',
            'version' => '2.0.2',
            'qualifiers' => [],
            'subpath' => null,
            'expected' => 'pkg:composer/guzzlehttp/promises@2.0.2',
        ],
        'npm scoped package encodes namespace segment' => [
            'type' => 'npm',
            'namespace' => '@angular',
            'name' => 'animation',
            'version' => '12.3.1',
            'qualifiers' => [],
            'subpath' => null,
            'expected' => 'pkg:npm/%40angular/animation@12.3.1',
        ],
        'maven qualifiers are sorted and encoded' => [
            'type' => 'maven',
            'namespace' => 'org.springframework',
            'name' => 'spring-core',
            'version' => '5.3.30',
            'qualifiers' => [
                'repository_url' => 'https://repo.spring.io/release',
                'type' => 'jar',
            ],
            'subpath' => null,
            'expected' => 'pkg:maven/org.springframework/spring-core@5.3.30?repository_url=https:%2F%2Frepo.spring.io%2Frelease&type=jar',
        ],
        'checksum qualifiers canonicalize key order and keep values explicit' => [
            'type' => 'generic',
            'namespace' => null,
            'name' => 'artifact',
            'version' => '1.0.0',
            'qualifiers' => [
                'download_url' => 'https://example.com/archive.tgz',
                'checksum' => 'sha256:abc123,sha512:def456',
            ],
            'subpath' => null,
            'expected' => 'pkg:generic/artifact@1.0.0?checksum=sha256:abc123,sha512:def456&download_url=https:%2F%2Fexample.com%2Farchive.tgz',
        ],
        'subpath cleanup removes dot segments and empties' => [
            'type' => 'generic',
            'namespace' => null,
            'name' => 'artifact',
            'version' => null,
            'qualifiers' => [],
            'subpath' => 'src/./docs//../guides/.//install',
            'expected' => 'pkg:generic/artifact#src/guides/install',
        ],
    ],
    'parse' => [
        'composer package exposes namespace name and version' => [
            'input' => 'pkg:composer/guzzlehttp/promises@2.0.2',
            'expected' => [
                'type' => 'composer',
                'namespace' => 'guzzlehttp',
                'name' => 'promises',
                'version' => '2.0.2',
                'qualifiers' => [],
                'subpath' => null,
                'canonical' => 'pkg:composer/guzzlehttp/promises@2.0.2',
            ],
        ],
        'npm scoped package decodes namespace' => [
            'input' => 'pkg:npm/%40angular/animation@12.3.1',
            'expected' => [
                'type' => 'npm',
                'namespace' => '@angular',
                'name' => 'animation',
                'version' => '12.3.1',
                'qualifiers' => [],
                'subpath' => null,
                'canonical' => 'pkg:npm/%40angular/animation@12.3.1',
            ],
        ],
        'maven package preserves qualifiers and encoded repository url' => [
            'input' => 'pkg:maven/org.springframework/spring-core@5.3.30?repository_url=https:%2F%2Frepo.spring.io%2Frelease&type=jar',
            'expected' => [
                'type' => 'maven',
                'namespace' => 'org.springframework',
                'name' => 'spring-core',
                'version' => '5.3.30',
                'qualifiers' => [
                    'repository_url' => 'https://repo.spring.io/release',
                    'type' => 'jar',
                ],
                'subpath' => null,
                'canonical' => 'pkg:maven/org.springframework/spring-core@5.3.30?repository_url=https:%2F%2Frepo.spring.io%2Frelease&type=jar',
            ],
        ],
        'subpath cleanup canonicalizes traversals' => [
            'input' => 'pkg:generic/artifact#src/./docs//../guides/.//install',
            'expected' => [
                'type' => 'generic',
                'namespace' => null,
                'name' => 'artifact',
                'version' => null,
                'qualifiers' => [],
                'subpath' => 'src/guides/install',
                'canonical' => 'pkg:generic/artifact#src/guides/install',
            ],
        ],
    ],
    'roundtrip' => [
        'qualifier order canonicalizes during roundtrip' => [
            'input' => 'pkg:maven/org.springframework/spring-core@5.3.30?type=jar&repository_url=https:%2F%2Frepo.spring.io%2Frelease',
            'canonical' => 'pkg:maven/org.springframework/spring-core@5.3.30?repository_url=https:%2F%2Frepo.spring.io%2Frelease&type=jar',
            'expected' => [
                'type' => 'maven',
                'namespace' => 'org.springframework',
                'name' => 'spring-core',
                'version' => '5.3.30',
                'qualifiers' => [
                    'repository_url' => 'https://repo.spring.io/release',
                    'type' => 'jar',
                ],
                'subpath' => null,
            ],
        ],
        'encoded version and checksum remain stable' => [
            'input' => 'pkg:generic/artifact@release%2F2026?download_url=https:%2F%2Fexample.com%2Farchive.tgz&checksum=sha256:abc123,sha512:def456',
            'canonical' => 'pkg:generic/artifact@release%2F2026?checksum=sha256:abc123,sha512:def456&download_url=https:%2F%2Fexample.com%2Farchive.tgz',
            'expected' => [
                'type' => 'generic',
                'namespace' => null,
                'name' => 'artifact',
                'version' => 'release/2026',
                'qualifiers' => [
                    'checksum' => 'sha256:abc123,sha512:def456',
                    'download_url' => 'https://example.com/archive.tgz',
                ],
                'subpath' => null,
            ],
        ],
        'subpath cleanup canonicalizes on stringify' => [
            'input' => 'pkg:generic/artifact#src/./docs//../guides/.//install',
            'canonical' => 'pkg:generic/artifact#src/guides/install',
            'expected' => [
                'type' => 'generic',
                'namespace' => null,
                'name' => 'artifact',
                'version' => null,
                'qualifiers' => [],
                'subpath' => 'src/guides/install',
            ],
        ],
    ],
    'invalid' => [
        'parse' => [
            'missing pkg scheme' => [
                'input' => 'composer/guzzlehttp/promises@2.0.2',
                'reason' => 'missing pkg:',
            ],
            'missing type' => [
                'input' => 'pkg:/promises@2.0.2',
                'reason' => 'missing type',
            ],
            'missing name' => [
                'input' => 'pkg:npm',
                'reason' => 'missing name',
            ],
            'invalid qualifier key' => [
                'input' => 'pkg:generic/artifact?in production=true',
                'reason' => 'invalid qualifier key',
            ],
            'malformed encoded separator' => [
                'input' => 'pkg:generic/artifact?download_url=https:%2F%2Fexample.com%2archive.tgz',
                'reason' => 'malformed percent encoding',
            ],
        ],
        'build' => [
            'missing build type' => [
                'type' => '',
                'namespace' => null,
                'name' => 'artifact',
                'version' => null,
                'qualifiers' => [],
                'subpath' => null,
                'reason' => 'missing type',
            ],
            'missing build name' => [
                'type' => 'generic',
                'namespace' => null,
                'name' => '',
                'version' => null,
                'qualifiers' => [],
                'subpath' => null,
                'reason' => 'missing name',
            ],
            'build rejects malformed qualifier keys' => [
                'type' => 'generic',
                'namespace' => null,
                'name' => 'artifact',
                'version' => null,
                'qualifiers' => [
                    'in production' => 'true',
                ],
                'subpath' => null,
                'reason' => 'invalid qualifier key',
            ],
        ],
    ],
];
