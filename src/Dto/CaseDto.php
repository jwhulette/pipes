<?php

declare(strict_types=1);

namespace jwhulette\pipes\Dto;

class CaseDto
{
    public function __construct(
        public readonly string|int $column,
        public readonly int $mode,
        public readonly string $encoding
    ) {
        // code...
    }
}
