<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Jwhulette\Pipes\Frame;
use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;

/**
 * Trim the item.
 */
class TrimTransformer implements TransformerInterface
{
    protected Collection $columns;
    protected bool $allcolumns = false;
    protected string $type = 'trim';
    protected string $mask = " \t\n\r\0\x0B";

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->columns = new Collection();
    }

    /**
     * @param string|int $column name|index
     * @param string|null $type trim|ltrim|rtrim
     * @param string|null $mask
     *
     * @return TrimTransformer
     */
    public function transformColumn(string|int $column, ?string $type = null, ?string $mask = null): TrimTransformer
    {
        $this->columns->push((object) [
            'column' => $column,
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ]);

        return $this;
    }

    /**
     * @param string|null $type trim|ltrim|rtrim
     * @param string|null $mask
     *
     * @return TrimTransformer
     */
    public function transformAllColumns(?string $type = null, ?string $mask = null): TrimTransformer
    {
        $this->columns->push((object) [
            'type' => $type ?? $this->type,
            'mask' => $mask ?? $this->mask,
        ]);

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
        if ($this->allcolumns === true) {
            $frame->data->transform(function ($item) {
                return $this->trimColumnValue(
                    $item,
                    $this->columns->first()->type,
                    $this->columns->first()->mask
                );
            });

            return $frame;
        }

        // Apply to only selected columns
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                if ($column->column === $key) {
                    return $this->trimColumnValue(
                        $item,
                        $column->type,
                        $column->mask
                    );
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
     * @throws \Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException
     */
    public function trimColumnValue(string $value, string $type, string $mask): string
    {
        if (!\is_callable($type)) {
            throw new PipesInvalidArgumentException("Invalid trim type: {$type}.");
        }

        return \call_user_func($type, $value, $mask);
    }
}
