<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class ConditionalTransformer implements TransformerInterface
{
    protected array $conditionals;

    /**
     * ConditionalTransformer.
     *
     * @param array<array> $conditionals
     */
    public function __construct(array $conditionals)
    {
        $this->conditionals = $conditionals;
    }

    /**
     * Invoke the transformer.
     *
     * @param Frame $frame
     *
     * @return Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        foreach ($this->conditionals as $conditional) {
            $frameArray = $frame->data->toArray();
            $check = \array_diff_assoc($conditional['match'], $frameArray);
            if (\count($check) === 0) {
                $replaced = \array_replace($frameArray, $conditional['replace']);
                $frame->data = collect($replaced);
            }
        }

        return $frame;
    }
}
