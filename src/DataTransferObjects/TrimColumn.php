<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class TrimColumn extends DataTransferObject
{
    public int|string $column;
    public string $type;
    public string $mask;

    public function __construct(int|string $column, string $type, string $mask)
    {
        $this->column = $column;
        $this->type = $type;
        $this->mask = $mask;
    }
}
