<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DateTimeColumn extends DataTransferObject
{
    public int|string $column;
    public string $outputFormat;
    public ?string $inputFormat = \null;

    public function __construct(int|string $column, string $outputFormat, ?string $inputFormat)
    {
        $this->column = $column;
        $this->outputFormat = $outputFormat;
        $this->inputFormat =$inputFormat;
    }
}
