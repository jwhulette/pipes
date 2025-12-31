<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

/**
 * @internal
 */
final readonly class ZipcodeDto
{
    public function __construct(
        public int|string $column,
        public int $maxlength,
        public ?int $option,
    ) {
    }
}
