<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

interface TransformerInterface
{
    public function __invoke(Frame $frame): Frame;
}
