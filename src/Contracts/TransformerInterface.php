<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Jwhulette\Pipes\Frame;

interface TransformerInterface
{
    public function __invoke(Frame $frame): Frame;
}
