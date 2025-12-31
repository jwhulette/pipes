<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\DataTransferObjects;

use function collect;
use Illuminate\Support\Collection;

/**
 * @internal
 */
final readonly class ConditionalDto
{
    /**
     * @var Collection<string, string>
     */
    public Collection $match;

    /**
     * @var Collection<string, string>
     */
    public Collection $replace;

    /**
     * @param array<string,string> $match
     * @param array<string,string> $replace
     */
    public function __construct(array $match, array $replace)
    {
        $this->match = collect($match);
        $this->replace = collect($replace);
    }
}
