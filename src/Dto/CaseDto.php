<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Dto;

/**
 * @internal
 */
final class CaseDto
{
    public function __construct(
        public readonly string|int $column,
        public readonly int $mode,
        public readonly string $encoding
    ) {
        // code...
    }
}
