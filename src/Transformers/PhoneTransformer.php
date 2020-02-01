<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class PhoneTransformer implements TransformerInterface
{
    protected array $columns = [];

    /**
     * @param string|int $column
     * @param int $limit
     *
     * @return PhoneTransformer
     */
    public function transformColumn($column, int $limit = 10): PhoneTransformer
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'limit' => $limit,
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

        if ($transform['limit'] > 0) {
            $transformed = \substr($transformed, 0, $transform['limit']);
        }

        return $transformed;
    }
}
