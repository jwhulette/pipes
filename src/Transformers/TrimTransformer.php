<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use InvalidArgumentException;
use jwhulette\pipes\Frame;

class TrimTransformer implements TransformerInterface
{
    /**
     * @var array<int|string,string|array<int|string,string>>
     */
    protected array $columns;

    protected bool $allColumns = false;

    protected string $type = 'trim';

    protected string $mask = " \t\n\r\0\x0B";

    public function transformColumn(string $column, ?string $type = null, ?string $mask = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ];

        return $this;
    }

    public function transformColumnByIndex(int $column, ?string $type = null, ?string $mask = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ];

        return $this;
    }

    public function transformAllColumns(?string $type = null, ?string $mask = null): self
    {
        $this->columns = [
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ];

        $this->allColumns = true;

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allColumns) {
            $frame->data->transform(fn ($item) => $this->trimColumnValue($item, $this->columns['type'], $this->columns['mask']));

            return $frame;
        }

        // Apply to only selected columns
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                /** @var string $key */
                if ($column['column'] === $key) {
                    return $this->trimColumnValue($item, $column['type'], $column['mask']);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function trimColumnValue(string $value, string $type, string $mask): string
    {
        if (! \is_callable($type)) {
            throw new InvalidArgumentException("Invalid trim type: {$type}.");
        }

        return \call_user_func($type, $value, $mask);
    }
}
