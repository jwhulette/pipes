<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PhoneColumn extends DataTransferObject
{
    public int|string $column;

    public int $maxLength;

    public function __construct(int|string $column, int $maxLength)
    {
        $this->column = $column;
        $this->maxLength = $maxLength;
    }
}
