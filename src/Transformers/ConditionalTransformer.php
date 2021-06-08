<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Frame;

/**
 * Change a value of an item of based on a conditional.
 */
class ConditionalTransformer implements TransformerInterface
{
    protected Collection $conditionals;

    /**
     * __construct.
     */
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
        $condition = [
            'match' => collect($match),
            'replace' => collect($replace),
        ];

        $this->conditionals->push($condition);

        return $this;
    }

    /**
     * @param Frame $frame
     *
     * @return Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        $this->conditionals->transform(function ($item) use ($frame) {
            $diff = $item['match']->diffAssoc($frame->data);

            if ($diff->count() === 0) {
                $frame->data = $frame->data->replace($item['replace']);
            }
        });

        return $frame;
    }
}
