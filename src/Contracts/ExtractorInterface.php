<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;

interface ExtractorInterface
{
    public function extract(): Generator;
}
