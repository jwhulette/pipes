<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Dto;

class DateTimeDto
{
    public function __construct(
        public readonly string|int $column,
        public readonly string $outputFormat,
        public readonly string $inputFormat
    ) {
        // code...
    }
}
