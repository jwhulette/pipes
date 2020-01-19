<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class TrimTransformer implements TransformerInterface
{
    protected array $columns = [];
    protected bool $allcolumns = false;
    protected string $type;
    protected string $mask;


    /**
     * Transfrom the column
     *
     * @param string $column
     * @param string $type
     * @param string $mask
     *
     * @return TrimTransformer
     */
    public function transformColumn(string $column, string $type = 'trim', string $mask = " \t\n\r\0\x0B"): TrimTransformer
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'type' => $type,
            'mask' => $mask
        ];

        return $this;
    }

    /**
     * Transfrom all columns
     *
     * @param string $type
     * @param string $mask
     *
     * @return TrimTransformer
     */
    public function transformAllColumns(string $type = 'trim', string $mask = " \t\n\r\0\x0B"): TrimTransformer
    {
        $this->type = $type;
        $this->mask = $mask;
        $this->allcolumns = true;

        return $this;
    }

    /**
     *  Invoke the transformer.
     *
     * @param \jwhulette\pipes\Frame $frame
     *
     * @return \jwhulette\pipes\Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allcolumns) {
            $frame->data->transform(function ($item) {
                return $this->trimColumnValue($item, $this->type, $this->mask);
            });
        }

        // Apply to only selected columns
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                if ($column['column'] === $key) {
                    return $this->trimColumnValue($item, $column['type'], $column['mask']);
                }
            }
            return $item;
        });

        return $frame;
    }

    /**
     *
     * @param string $value
     * @param string $type
     * @param string $mask
     *
     * @return string
     */
    public function trimColumnValue(string $value, string $type, string $mask): string
    {
        return \call_user_func($type, $value, $mask);
    }
}
