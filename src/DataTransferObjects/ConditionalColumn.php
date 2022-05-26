<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class ConditionalColumn extends DataTransferObject
{
    public Collection $match;

    public Collection $replace;

    /**
     * @param  array<string> $match
     * @param  array<string> $replace
     */
    public function __construct(array $match, array $replace)
    {
        $this->match = collect($match);
        $this->replace = collect($replace);
    }
}
