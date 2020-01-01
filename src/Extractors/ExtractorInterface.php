<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;

interface ExtractorInterface
{
    public function extract(): Generator;
}
