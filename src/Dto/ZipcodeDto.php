<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Dto;

/**
 * @internal
 */
final class ZipcodeDto
{
    public function __construct(
        public readonly int|string $column,
        public readonly int $maxlength,
        public readonly ?int $option
    ) {
    }
}
