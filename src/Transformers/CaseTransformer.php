<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class CaseTransformer implements TransformerInterface
{
    protected array $columns = [];

    /**
     * Set the column to transform.
     *
     * @param string  $column
     * @param string $mode upper|lower|title
     * @param string $encoding
     *
     * @return CaseTransformer
     */
    public function transformColumn(string $column, string $mode, string $encoding = 'utf-8'): self
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'mode' => $this->getMode($mode),
            'encoding' => $encoding,
        ];

        return $this;
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
            foreach ($this->columns as $column) {
                if ($column['column'] === $key) {
                    return \mb_convert_case($item, $column['mode'], $column['encoding']);
                }
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
        switch (strtolower($mode)) {
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
