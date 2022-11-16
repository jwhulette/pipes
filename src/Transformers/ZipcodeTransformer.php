<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Dto\ZipcodeDto;
use jwhulette\pipes\Frame;

class ZipcodeTransformer implements TransformerInterface
{
    /**
     * @var array<int,\jwhulette\pipes\Dto\ZipcodeDto>
     */
    protected array $columns;

    protected int $maxlength = 5;

    public function tranformColumn(string|int $column, ?string $pad = null, ?int $maxlength = null): self
    {
        $length = $maxlength ?? $this->maxlength;
        $this->columns[] = new ZipcodeDto($column, $length, $this->setOption($pad));

        return $this;
    }

    private function setOption(?string $option): ?int
    {
        if (! \is_null($option)) {
            if (strtolower($option) === 'padleft') {
                return STR_PAD_LEFT;
            }

            if (strtolower($option) === 'padright') {
                return STR_PAD_RIGHT;
            }
        }

        return null;
    }

    /**
     * @param Frame $frame
     *
     * @return Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $dto) {
                if ($dto->column === $key) {
                    return $this->transformZipcode($item, $dto->option, $dto->maxlength);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $zipcode
     * @param int|null $type
     * @param int $maxlength
     *
     * @return string
     */
    private function transformZipcode(string $zipcode, ?int $type, int $maxlength): string
    {
        $transformed = \strval(\preg_replace('/\D+/', '', $zipcode));

        $zipLength = \strlen($transformed);

        if ($zipLength > $maxlength) {
            return \substr($transformed, 0, $maxlength);
        }

        if (! \is_null($type) && $zipLength < $maxlength) {
            return \str_pad($transformed, $maxlength, '0', $type);
        }

        return $transformed;
    }
}
