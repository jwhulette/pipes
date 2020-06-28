<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;
use InvalidArgumentException;

class CaseTransformer implements TransformerInterface
{
    protected array $transformers = [];

    /**
     * @param string $column
     * @param string $mode upper|lower|title
     * @param string $encoding
     *
     * @return CaseTransformer
     */
    public function transformColumn(string $column, string $mode, string $encoding = 'utf-8'): CaseTransformer
    {
        $this->transformers[] = (object) [
            'column' =>  $column,
            'mode' => $this->getMode($mode),
            'encoding' => $encoding,
        ];

        return $this;
    }

    /**
     * @param int $column
     * @param string $mode upper|lower|title
     * @param string $encoding
     *
     * @return CaseTransformer
     */
    public function transformColumnByIndex(int $column, string $mode, string $encoding = 'utf-8'): CaseTransformer
    {
        $this->transformers[] = (object) [
            'column' => $column,
            'mode' => $this->getMode($mode),
            'encoding' => $encoding,
        ];

        return $this;
    }

    /**
     * @param Frame $frame
     *
     * @return Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->transformers as $transformer) {
                if ($transformer->column === $key) {
                    return \mb_convert_case($item, $transformer->mode, $transformer->encoding);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $mode
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    private function getMode(string $mode): int
    {
        switch (strtolower($mode)) {
            case 'upper':
                return MB_CASE_UPPER;
            case 'lower':
                return MB_CASE_LOWER;
            case 'title':
                return MB_CASE_TITLE;
        }
        throw new InvalidArgumentException("Invalid conversion mode {$mode}.");
    }
}
