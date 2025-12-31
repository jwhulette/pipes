<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

/**
 * @internal
 */
final readonly class PhoneDto
{
    public function __construct(
        public string|int $column,
        public int $maxlength,
    ) {
    }
}
