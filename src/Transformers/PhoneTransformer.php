<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Jwhulette\Pipes\Frame;
use Illuminate\Support\Collection;

/**
 * Clean phone numbers to include only digits.
 */
class PhoneTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected int $maxlength = 10;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->columns = new Collection;
    }

    /**
     * @param mixed $column name|index
     * @param int $maxlength
     *
     * @return PhoneTransformer
     */
    public function transformColumn($column, int $maxlength = null): PhoneTransformer
    {
        $this->columns->push((object)[
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
                if ($column->column === $key) {
                    return $this->tranformPhone($item, $column);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $item
     * @param object $transform
     *
     * @return string
     */
    private function tranformPhone(string $item, object $transform): string
    {
        // Remove all non numeric characters
        $transformed = \preg_replace('/\D+/', '', $item);

        if ($transform->maxlength > 0) {
            $transformed = \substr($transformed, 0, $transform->maxlength);
        }

        return $transformed;
    }
}
