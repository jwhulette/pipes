<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\CaseDto;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;

final class CaseTransformer implements TransformerInterface
{
    /**
     * @var array<int,CaseDto>
     */
    protected array $transformers;

    public function __invoke(Frame $frame): Frame
    {
        $frame->getData()->transform(function ($item, $key) {
            foreach ($this->transformers as $transformer) {
                if ($transformer->column === $key) {
                    return mb_convert_case(
                        strval($item),
                        $transformer->mode,
                        $transformer->encoding
                    );
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * Set the columns and transformation.
     *
     * @param string $mode [upper, lower, title ]
     */
    public function transformColumn(string|int $column, string $mode, string $encoding = 'utf-8'): self
    {
        $this->transformers[] = new CaseDto($column, $this->getMode($mode), $encoding);

        return $this;
    }

    /**
     * @throws PipesInvalidArgumentException
     */
    private function getMode(string $mode): int
    {
        return match (strtolower($mode)) {
            'upper' => MB_CASE_UPPER,
            'lower' => MB_CASE_LOWER,
            'title' => MB_CASE_TITLE,
            default => throw new PipesInvalidArgumentException("Invalid conversion mode {$mode}."),
        };
    }
}
