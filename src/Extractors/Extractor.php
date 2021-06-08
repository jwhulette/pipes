<?php

namespace Jwhulette\Pipes\Extractors;

use Jwhulette\Pipes\Frame;

abstract class Extractor
{
    protected Frame $frame;

    protected string $file;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;
}
