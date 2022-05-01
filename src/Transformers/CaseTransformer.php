<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\CaseColumn;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;

/**
 * Change the case of the item.
 */
class CaseTransformer implements TransformerInterface
{
    protected Collection $transformers;

    public function __construct()
    {
        $this->transformers = new Collection();
    }

    public function transformColumn(int|string $column, string $mode, string $encoding = 'utf-8'): self
    {
        $transformer = new CaseColumn(
            $column,
            $this->getMode($mode),
            $encoding,
        );

        $this->transformers->push($transformer);

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->getData()->transform(function ($item, $key) {
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
