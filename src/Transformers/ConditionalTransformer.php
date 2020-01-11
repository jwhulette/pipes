<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class ConditionalTransformer implements TransformerInterface
{
    /** @var array */
    protected $conditionals;
 
    /**
     * ConditionalTransformer.
     *
     * @param array $conditionals
     */
    public function __construct(array $conditionals)
    {
        $this->conditionals = $conditionals;
    }

    /**
     * Invoke the transformer.
     *
     * @param \jwhulette\pipes\Frame $frame
     *
     * @return \jwhulette\pipes\Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        foreach ($this->conditionals as $conditional) {
            $check = \array_diff_assoc($conditional['match'], $frame->data->toArray());
            if (\count($check) === 0) {
                $replaced = \array_replace($frame->data->toArray(), $conditional['replace']);
                $frame->data = collect($replaced);
            }
        }

        return $frame;
    }
}
