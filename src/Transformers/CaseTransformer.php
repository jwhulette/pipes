<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Jwhulette\Pipes\Frame;
use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;

/**
 * Change the case of the item.
 */
class CaseTransformer implements TransformerInterface
{
    protected Collection $transformers;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->transformers = new Collection();
    }

    /**
     * @param mixed $column name|index
     * @param string $mode upper|lower|title
     * @param string $encoding
     *
     * @return CaseTransformer
     */
    public function transformColumn($column, string $mode, string $encoding = 'utf-8'): CaseTransformer
    {
        $transformer = (object) [
            'column' =>  $column,
            'mode' => $this->getMode($mode),
            'encoding' => $encoding,
        ];

        $this->transformers->push($transformer);

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
     * @throws PipesInvalidArgumentException
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

        throw new PipesInvalidArgumentException("Invalid conversion mode {$mode}.");
    }
}
