<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\ConditionalColumn;
use Jwhulette\Pipes\Frame;

/**
 * Change a value of an item of based on a conditional.
 */
class ConditionalTransformer implements TransformerInterface
{
    protected Collection $conditionals;

    public function __construct()
    {
        $this->conditionals = new Collection();
    }

    /**
     * @param array $match Any associative array of keys to values to match against
     * @param array $replace An associative array of keys to values to replace
     *
     * @return ConditionalTransformer
     */
    public function addConditional(array $match, array $replace): ConditionalTransformer
    {
        $condition = new ConditionalColumn($match, $replace);

        $this->conditionals->push($condition);

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $this->conditionals->transform(function ($item) use ($frame) {
            $diff = $item->match->diffAssoc($frame->getData());

            if ($diff->count() === 0) {
                $frame->setData($frame->getData()->replace($item->replace)->toArray());
            }
        });

        return $frame;
    }
}
