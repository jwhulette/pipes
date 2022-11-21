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
     * @var \Illuminate\Support\Collection<int,ConditionalDto>
     */
    protected Collection $conditionals;

    public function __construct()
    {
        $this->conditionals = new Collection();
    }

    /**
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

    public function __invoke(Frame $frame): Frame
    {
        // @phpstan-ignore-next-line
        $this->conditionals->transform(function (ConditionalDto $item) use ($frame): void {
            $diff = $item->match->diffAssoc($frame->data);

            if ($diff->count() === 0) {
                $frame->data = $frame->data->replace($item->replace);
            }
        });

        return $frame;
    }
}
