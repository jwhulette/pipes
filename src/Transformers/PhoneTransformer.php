<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use function is_null;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\PhoneDto;
use Jwhulette\Pipes\Frame;
use function preg_replace;
use function strval;
use function substr;

final class PhoneTransformer implements TransformerInterface
{
    /** @var array<int,PhoneDto> */
    protected array $columns;

    protected int $maxlength = 10;

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $dto) {
                if ($dto->column === $key) {
                    return $this->tranformPhone(
                        strval($item),
                        $dto
                    );
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param PhoneDto $phoneDto $transform
     */
    private function tranformPhone(?string $item, PhoneDto $phoneDto): string
    {
        if (is_null($item)) {
            return '';
        }

        // Remove all non numeric characters
        $transformed = strval(preg_replace('/\D+/', '', $item));

        if ($phoneDto->maxlength > 0) {
            $transformed = substr($transformed, 0, $phoneDto->maxlength);
        }

        return $transformed;
    }

    /**
     * Set the columns and transformation.
     *
     * @param  int|null  $maxlength  [Default: 10]
     */
    public function transformColumn(string|int $column, ?int $maxlength = null): self
    {
        $phoneMaxLength = $maxlength ?? $this->maxlength;

        $this->columns[] = new PhoneDto($column, $phoneMaxLength);

        return $this;
    }
}
