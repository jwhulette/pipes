<?php

namespace jwhulette\pipes\Extractors;

use jwhulette\pipes\Frame;

abstract class Extractor
{
    protected Frame $frame;

    protected string $file;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;
}
