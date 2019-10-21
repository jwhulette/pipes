<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use jwhulette\pipes\Frame;

interface LoaderInterface
{
    public function load(Frame $frame): void;
}
