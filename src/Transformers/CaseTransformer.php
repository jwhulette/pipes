<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use InvalidArgumentException;
use Jwhulette\Pipes\Dto\CaseDto;
use Jwhulette\Pipes\Frame;

final class CaseTransformer implements TransformerInterface
{
    /**
     * @var array<int,\Jwhulette\Pipes\Dto\CaseDto>
     */
    protected array $transformers;

    public function transformColumn(string|int $column, string $mode, string $encoding = 'utf-8'): self
    {
        $this->transformers[] = new CaseDto($column, $this->getMode($mode), $encoding);

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->transformers as $transformer) {
                if ($transformer->column === $key) {
                    return \mb_convert_case(\strval($item), $transformer->mode, $transformer->encoding);
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
