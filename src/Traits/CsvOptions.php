<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Traits;

trait CsvOptions
{
    protected bool $hasHeader = \true;
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';
    protected string $newline = '\n';
}
