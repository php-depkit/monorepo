<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Exception\Build;

use PhpDepkit\Purl\Exception\InvalidPurl;

final class MissingRequiredComponent extends InvalidPurl
{
    public static function for(string $component): self
    {
        return new self(
            'Missing required package URL component.',
            [
                'component' => $component,
                'reason' => 'missing',
            ],
        );
    }
}
