<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;
use Illuminate\Support\Collection;

/**
 * Clean phone numbers to include only digits.
 */
class PhoneTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected int $maxlength = 10;

    /**
     * @param string $column
     * @param int $maxlength
     *
     * @return PhoneTransformer
     */
    public function transformColumnByName(string $column, int $maxlength = null): PhoneTransformer
    {
        $this->columns->push([
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
        ]);

        return $this;
    }

    /**
     * @param int $column
     * @param int $maxlength
     *
     * @return PhoneTransformer
     */
    public function transformColumnByIndex(int $column, int $maxlength = null): PhoneTransformer
    {
        $this->columns->push([
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
        ]);

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
            foreach ($this->columns as $column) {
                if ($column['column'] === $key) {
                    return $this->tranformPhone($item, $column);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $item
     * @param array $transform
     *
     * @return string
     */
    private function tranformPhone(string $item, array $transform): string
    {
        // Remove all non numeric characters
        $transformed = \preg_replace('/\D+/', '', $item);

        if ($transform['maxlength'] > 0) {
            $transformed = \substr($transformed, 0, $transform['maxlength']);
        }

        return $transformed;
    }
}
