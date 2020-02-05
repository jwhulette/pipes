<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class TrimTransformer implements TransformerInterface
{
    protected array $columns = [];
    protected bool $allcolumns = false;
    protected string $type = 'trim';
    protected string $mask = " \t\n\r\0\x0B";

    /**
     * @param string $column
     * @param string $type
     * @param string $mask
     *
     * @return TrimTransformer
     */
    public function transformColumn(string $column, ?string $type = null, ?string $mask = null): TrimTransformer
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ];

        return $this;
    }

    /**
     * @param string $type
     * @param string $mask
     *
     * @return TrimTransformer
     */
    public function transformAllColumns(?string $type = null, ?string $mask = null): TrimTransformer
    {
        $this->columns = [
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ];
        $this->allcolumns = true;

        return $this;
    }

    /**
     * @param Frame $frame
     *
     * @return Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allcolumns) {
            $frame->data->transform(function ($item) {
                return $this->trimColumnValue($item, $this->columns['type'], $this->columns['mask']);
            });

            return $frame;
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
     * @param string $value
     * @param string $type
     * @param string $mask
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function trimColumnValue(string $value, string $type, string $mask): string
    {
        if (! \is_callable($type)) {
            throw new \InvalidArgumentException("Invalid trim type: {$type}.");
        }

        return \call_user_func($type, $value, $mask);
    }
}
