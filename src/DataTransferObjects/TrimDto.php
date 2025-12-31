<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

/**
 * @internal
 */
final readonly class TrimDto
{
    public function __construct(
        public int|string|null $column,
        public ?string $type,
        public ?string $mask,
    ) {
    }
}
