<?php

declare(strict_types=1);

namespace jwhulette\pipes\Dto;

class PhoneDto
{
    public function __construct(
        public readonly string|int $column,
        public readonly int $maxlength
    ) {
    }
}