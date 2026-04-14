<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/packages',
        __DIR__ . '/tools',
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::COMMON,
        SetList::ARRAY,
        SetList::COMMENTS,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::SPACES,
        SetList::STRICT,
    ]);

    $ecsConfig->skip([
        __DIR__ . '/vendor/*',
    ]);
};
