<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Exception\Build;

use PhpDepkit\Purl\Exception\InvalidPurl;

final class InvalidQualifierKey extends InvalidPurl
{
    public static function forBuild(): self
    {
        return new self(
            'Invalid package URL qualifier.',
            [
                'component' => 'qualifiers',
                'reason' => 'invalid_key',
            ],
        );
    }
}
