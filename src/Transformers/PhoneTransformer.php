<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\PhoneColumn;
use Jwhulette\Pipes\Frame;

/**
 * Clean phone numbers to include only digits.
 */
class PhoneTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected int $maxlength = 10;

    public function __construct()
    {
        $this->columns = new Collection();
    }

    public function transformColumn(int|string $column, int $maxlength = null): PhoneTransformer
    {
        $this->columns->push(new PhoneColumn($column, $maxlength ?? $this->maxlength));

        return $this;
    }

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

    private function tranformPhone(string $item, PhoneColumn $transform): string
    {
        // Remove all non numeric characters
        $transformed = (string) \preg_replace('/\D+/', '', $item);

        if ($transform->maxLength > 0) {
            $transformed = \substr($transformed, 0, $transform->maxLength);
        }

        return $transformed;
    }
}
