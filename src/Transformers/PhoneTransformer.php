<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Dto\PhoneDto;
use jwhulette\pipes\Frame;

class PhoneTransformer implements TransformerInterface
{
    /**
     * @var array<int,\jwhulette\pipes\Dto\PhoneDto>
     */
    protected array $columns;

    protected int $maxlength = 10;

    public function transformColumn(string|int $column, int $maxlength = null): self
    {
        $phoneMaxLength = $maxlength ?? $this->maxlength;

        $this->columns[] = new PhoneDto($column, $phoneMaxLength);

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $dto) {
                if ($dto->column === $key) {
                    return $this->tranformPhone(\strval($item), $dto);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $item
     * @param PhoneDto $phoneDto $transform
     *
     * @return string
     */
    private function tranformPhone(?string $item, PhoneDto $phoneDto): string
    {
        if (\is_null($item)) {
            return '';
        }

        // Remove all non numeric characters
        $transformed = \strval(\preg_replace('/\D+/', '', $item));

        if ($phoneDto->maxlength > 0) {
            $transformed = \substr($transformed, 0, $phoneDto->maxlength);
        }

        return $transformed;
    }
}
