<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Contracts;

use Jwhulette\Pipes\Frame;

interface TransformerInterface
{
    public function __invoke(Frame $frame): Frame;
}
