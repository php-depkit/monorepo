<?php

declare(strict_types=1);

namespace PhpDepkit\Purl\Exception;

use InvalidArgumentException;

class InvalidPurl extends InvalidArgumentException
{
    /**
     * @param array{component: string, reason: string} $context
     */
    public function __construct(
        string $message,
        private readonly array $context,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return array{component: string, reason: string}
     */
    public function context(): array
    {
        return $this->context;
    }
}
