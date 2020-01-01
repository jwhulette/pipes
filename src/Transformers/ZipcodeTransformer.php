<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use Exception;
use jwhulette\pipes\Frame;

class ZipcodeTransformer implements TransformerInterface
{
    /** @var array */
    protected $columns;

    protected $limit;

    /** @var bool */
    protected $zero;

    /** @var int */
    protected $pad;

    /**
     * ZipcodeTranformer.
     *
     * @param array $columns
     * @param int $limit
     */
    public function __construct(array $columns, int $limit = 5)
    {
        $this->columns = $columns;
        $this->limit = $limit;
    }

    /**
     * Set the zip code to all zeros if empty.
     */
    public function setToZero(): void
    {
        $this->zero = true;
    }

    /**
     * Pad the zip code with zero's on the left of any numbers
     */
    public function padLeft(): void
    {
        $this->pad = STR_PAD_LEFT;
    }

    /**
     * Pad the zip code with zero's on the right of any numbers
     */
    public function padRight(): void
    {
        $this->pad = STR_PAD_RIGHT;
    }

    /**
     * Invoke the transformer.
     *
     * @param \jwhulette\pipes\Frame $frame
     *
     * @return \jwhulette\pipes\Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            if (in_array(($key), $this->columns, true)) {
                return $this->transformZipcode($item);
            }

            return $item;
        });

        return $frame;
    }

    /**
     * Transform the phone.
     *
     * @param string $zipcode
     *
     * @return string|null
     */
    private function transformZipcode(string $zipcode): ?string
    {
        $transformed = \preg_replace('/\D+/', '', $zipcode);

        if (\strlen($transformed) > $this->limit) {
            return \substr($transformed, 0, $this->limit);
        }

        if (\strlen($transformed) < $this->limit) {
            if (isset($this->zero)) {
                return \str_pad($transformed, $this->limit, '0', STR_PAD_RIGHT);
            }

            if (isset($this->pad)) {
                return \str_pad($transformed, $this->limit, '0', $this->pad);
            }
        }

        return $transformed;
    }
}
