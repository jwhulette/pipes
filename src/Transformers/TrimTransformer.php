<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\TrimColumn;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;

/**
 * Trim the item.
 */
class TrimTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected bool $allColumns = false;

    protected string $type = 'trim';

    protected string $mask = " \t\n\r\0\x0B";

    public function __construct()
    {
        $this->columns = new Collection();
    }

    public function transformColumn(string | int $column, ?string $type = null, ?string $mask = null): self
    {
        $this->columns->push(new TrimColumn($column, $type ?? $this->type, $mask ?? $this->mask));

        return $this;
    }

    /**
     * @param string|null $type trim|ltrim|rtrim
     */
    public function transformAllColumns(?string $type = null, ?string $mask = null): self
    {
        $this->columns->push(new TrimColumn(0, $type ?? $this->type, $mask ?? $this->mask));

        $this->allColumns = true;

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allColumns === true) {
            $frame->getData()->transform(function ($item) {
                return $this->trimColumnValue(
                    $item,
                    $this->columns->first()->type,
                    $this->columns->first()->mask
                );
            });

            return $frame;
        }

        // Apply to only selected columns
        $frame->getData()->transform(function ($item, $key) {
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
     * @throws PipesInvalidArgumentException
     */
    protected function trimColumnValue(string $value, string $type, string $mask): string
    {
        if (! \is_callable($type)) {
            throw new PipesInvalidArgumentException("Invalid trim type: {$type}.");
        }

        return \call_user_func($type, $value, $mask);
    }
}
