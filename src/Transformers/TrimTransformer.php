<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class TrimTransformer implements TransformerInterface
{
    /** @var array */
    protected $columns;

    /** @var bool */
    protected $allcolumns = false;

    /** @var string */
    protected $mask;

    /** @var string */
    protected $type;

    /**
     * @param array  $columns
     * @param string $type
     * @param string $mask
     */
    public function __construct(array $columns = [], string $type = 'trim', string $mask = " \t\n\r\0\x0B")
    {
        $this->columns = $columns;

        if (\count($this->columns) === 0) {
            $this->allcolumns = true;
        }

        $this->mask = $mask;

        $this->type = $type;
    }

    /**
     *  Invoke the transformer.
     *
     * @param \jwhulette\pipes\Frame $frame
     *
     * @return \jwhulette\pipes\Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allcolumns) {
            $frame->data->transform(function ($item) {
                return \call_user_func($this->type, $item, $this->mask);
            });
        }

        // Apply to only selected columns
        $frame->data->transform(function ($item, $key) {
            if (\in_array(($key), $this->columns, true)) {
                return \call_user_func($this->type, $item, $this->mask);
            }

            return $item;
        });

        return $frame;
    }
}
