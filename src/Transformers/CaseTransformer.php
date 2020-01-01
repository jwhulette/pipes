<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class CaseTransformer implements TransformerInterface
{
    /** @var array */
    protected $columns;

    /** @var int */
    protected $mode;

    /** @var string */
    protected $encoding;

    /**
     * CaseTransformer.
     *
     * @param array  $columns
     * @param string $mode
     * @param string $encoding
     */
    public function __construct(array $columns, string $mode, string $encoding = 'utf-8')
    {
        $this->columns = $columns;
        $this->mode = $this->getMode($mode);
        $this->encoding = $encoding;
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
        $frame->data->transform(function ($item, $key) {
            if (in_array(($key), $this->columns, true)) {
                return mb_convert_case($item, $this->mode, $this->encoding);
            }


            return $item;
        });

        return $frame;
    }

    /**
     * Get the mode.
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    private function getMode($mode): int
    {
        switch ($mode) {
            case 'upper':
                return MB_CASE_UPPER;
            case 'lower':
                return MB_CASE_LOWER;
            case 'title':
                return MB_CASE_TITLE;
        }
        throw new \InvalidArgumentException("The conversion mode [{$mode}] is invalid.");
    }
}
