<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;
use Illuminate\Support\Collection;

class ConditionalTransformer implements TransformerInterface
{
    protected Collection $conditionals;

    public function __construct()
    {
        $this->conditionals = collect();
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
