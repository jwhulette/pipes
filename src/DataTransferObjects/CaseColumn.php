<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class CaseColumn extends DataTransferObject
{
    public int|string $column;
    public int $mode;
    public string $encoding;

    public function __construct(int|string $column, int $mode, string $encoding)
    {
        $this->column = $column;
        $this->mode = $mode;
        $this->encoding = $encoding;
    }
}
