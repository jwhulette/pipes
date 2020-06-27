<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class ZipcodeTransformer implements TransformerInterface
{
    protected array $columns = [];
    protected int $maxlength = 5;

    /**
     * @param string $column
     * @param string|null $pad padleft|padright
     * @param int|null $maxlength
     *
     * @return ZipcodeTransformer
     */
    public function tranformColumn(string $column, ?string $pad = null, ?int $maxlength = null): ZipcodeTransformer
    {
        $this->columns[] = [
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
            'option' => $this->setOption($pad),
        ];

        return $this;
    }

    /**
     * @param int $column
     * @param string|null $pad padleft|padright
     * @param int|null $maxlength
     *
     * @return ZipcodeTransformer
     */
    public function tranformColumnByIndex(int $column, ?string $pad = null, ?int $maxlength = null): ZipcodeTransformer
    {
        $this->columns[] = [
            'column' => $column,
            'maxlength' => $maxlength ?? $this->maxlength,
            'option' => $this->setOption($pad),
        ];

        return $this;
    }

    /**
     * @param string|null $option
     *
     * @return int|null
     */
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
            foreach ($this->columns as $column) {
                if ($column['column'] === $key) {
                    return $this->transformZipcode($item, $column['option'], $column['maxlength']);
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
        $transformed = \preg_replace('/\D+/', '', $zipcode);

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
