<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use jwhulette\pipes\Frame;

class PhoneTransformer implements TransformerInterface
{
    /** @var array */
    protected $columns;

    /** @var int */
    protected $limit;

    /**
     * PhoneTransformer.
     *
     * @param array $columns
     */
    public function __construct(array $columns, int $limit = 10)
    {
        $this->columns = $columns;
        $this->limit = $limit;
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
            if (\in_array(($key), $this->columns, true)) {
                return $this->tranformPhone($item);
            }

            return $item;
        });

        return $frame;
    }

    /**
     * Transform the phone.
     *
     * @param string $phone
     *
     * @return string
     */
    private function tranformPhone(string $phone): string
    {
        // Remove all non numeric characters
        $transformed = \preg_replace('/\D+/', '', $phone);

        if ($this->limit > 0) {
            $transformed = \substr($transformed, 0, $this->limit);
        }

        return $transformed;
    }
}
