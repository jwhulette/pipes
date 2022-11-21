<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Dto;

use Illuminate\Support\Collection;

/**
 * @internal
 */
final class ConditionalDto
{
    /**
     * @var \Illuminate\Support\Collection<string,string>
     */
    public readonly Collection $match;

    /**
     * @var \Illuminate\Support\Collection<string,string>
     */
    public readonly Collection $replace;

    /**
     * @param array<string,string> $match
     * @param array<string,string> $replace
     */
    public function __construct(array $match, array $replace)
    {
        $this->match = \collect($match);
        $this->replace = \collect($replace);
    }
}
