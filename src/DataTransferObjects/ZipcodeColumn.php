<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ZipcodeColumn extends DataTransferObject
{
    public int|string $column;

    public int $maxlength;

    public ?int $option = \null;

    public function __construct(int|string $column, int $maxlength, ?int $option)
    {
        $this->column = $column;
        $this->maxlength = $maxlength;
        $this->option = $option;
    }
}
