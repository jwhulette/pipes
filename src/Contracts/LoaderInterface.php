<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Contracts;

use Jwhulette\Pipes\Frame;

interface LoaderInterface
{
    public function load(Frame $frame): void;
}
