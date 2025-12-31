<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

/**
 * @internal
 */
final readonly class DateTimeDto
{
    public function __construct(
        public string|int $column,
        public ?string $outputFormat,
        public ?string $inputFormat,
    ) {
    }
}
