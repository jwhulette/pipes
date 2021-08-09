<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Contracts;

use Generator;

interface ExtractorInterface
{
    public function extract(): Generator;
}
