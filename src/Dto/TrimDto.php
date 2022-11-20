<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Dto;

class TrimDto
{
    public function __construct(
        public readonly int|string|null $column,
        public readonly ?string $type,
        public readonly ?string $mask
    ) {
    }
}
