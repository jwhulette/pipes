<?php

declare(strict_types=1);

namespace jwhulette\pipes\Dto;

class ZipcodeDto
{
    public function __construct(
        public readonly int|string $column,
        public readonly ?int $maxlength,
        public readonly ?int $option
    ) {
    }
}
