<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class PhoneTransformer implements TransformerInterface
{
    /**
     * @var array<int|string,string|array<int|string,string>>
     */
    protected array $columns;

    protected int $maxlength = 10;

    public function transformColumn(string $column, int $maxlength = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
        ];

        return $this;
    }

    public function transformColumnByIndex(int $column, int $maxlength = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
        ];

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                /** @var string $key */
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
     * @param array<int|string,int|string> $transform
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
