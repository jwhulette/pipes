<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use InvalidArgumentException;
use Jwhulette\Pipes\Dto\TrimDto;
use Jwhulette\Pipes\Frame;

final class TrimTransformer implements TransformerInterface
{
    /**
     * @var array<int,\Jwhulette\Pipes\Dto\TrimDto>
     */
    protected array $columns;

    protected bool $allColumns = false;

    protected string $type = 'trim';

    protected string $mask = " \t\n\r\0\x0B";

    public function transformColumn(string|int $column, ?string $type = null, ?string $mask = null): self
    {
        $columnType = $type ?? $this->type;
        $columnMask = $mask ?? $this->mask;
        $this->columns[] = new TrimDto($column, $columnType, $columnMask);

        return $this;
    }

    public function transformAllColumns(?string $type = null, ?string $mask = null): self
    {
        $columnType = $type ?? $this->type;
        $columnMask = $mask ?? $this->mask;

        $this->columns[] = new TrimDto(null, $columnType, $columnMask);

        $this->allColumns = true;

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        // Apply to all columns
        if ($this->allColumns) {
            $frame->data->transform(
                fn ($item) => $this->trimColumnValue(
                    \strval($item),
                    $this->columns[0]->type,
                    $this->columns[0]->mask
                )
            );

            return $frame;
        }

        // Apply to only selected columns
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $dto) {
                if ($dto->column === $key) {
                    return $this->trimColumnValue(\strval($item), $dto->type, $dto->mask);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function trimColumnValue(?string $value, ?string $type, ?string $mask): string
    {
        if (! \is_callable($type)) {
            throw new InvalidArgumentException("Invalid trim type: {$type}.");
        }

        if (\is_null($value)) {
            return '';
        }

        if (\is_null($mask)) {
            $mask = $this->mask;
        }

        $result = \call_user_func($type, $value, $mask);

        return \strval($result);
    }
}
