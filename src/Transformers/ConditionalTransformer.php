<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\ConditionalDto;
use Jwhulette\Pipes\Frame;

final class ConditionalTransformer implements TransformerInterface
{
    /**
     * @var Collection<int, ConditionalDto>
     */
    protected Collection $conditionals;

    public function __construct()
    {
        $this->conditionals = new Collection();
    }

    public function __invoke(Frame $frame): Frame
    {
        $this->conditionals->transform(function (ConditionalDto $item) use ($frame): void {
            /** @phpstan-ignore-next-line */
            $diff = $item->match->diffAssoc($frame->data);

            if ($diff->count() === 0) {
                /* @phpstan-ignore-next-line */
                $frame->data = $frame->data->replace($item->replace);
            }
        });

        return $frame;
    }

    /**
     * Add a conditional.
     *
     * @param array<string,string> $match Any associative array of keys to values to match against
     * @param array<string,string> $replace An associative array of keys to values to replace
     *
     * @return ConditionalTransformer
     */
    public function addConditional(array $match, array $replace): self
    {
        $condition = new ConditionalDto($match, $replace);

        $this->conditionals->push($condition);

        return $this;
    }
}
