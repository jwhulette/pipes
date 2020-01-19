<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use Exception;
use jwhulette\pipes\Frame;

class ZipcodeTransformer implements TransformerInterface
{
    protected array $columns = [];
    protected int $limit = 5;

    /**
     * Tansfrom column
     *
     * @param string $column
     * @param string|null $option padleft|padright
     * @param int|null $limit
     *
     * @return ZipcodeTransformer
     */
    public function tranformColumn(string $column, ?string $option = null, ?int $limit = null): ZipcodeTransformer
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'limit' => $limit ? $limit : $this->limit,
            'option' => $this->setOption($option)
        ];
        
        return $this;
    }

    /**
     * Set the column option
     *
     * @param string|null $option
     *
     * @return int|null
     */
    private function setOption(?string $option): ?int
    {
        if (!\is_null($option)) {
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
     * Invoke the transformer.
     *
     * @param \jwhulette\pipes\Frame $frame
     *
     * @return \jwhulette\pipes\Frame
     */
    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                if ($column['column'] === $key) {
                    return $this->transformZipcode($item, $column['option'], $column['limit']);
                }
            }
            return $item;
        });

        return $frame;
    }

    /*
     * Transform the zipcode.
     *
     * @param string $zipcode
     *
     * @return string|null
     */
    private function transformZipcode(string $zipcode, ?int $type, $limit): string
    {
        $transformed = \preg_replace('/\D+/', '', $zipcode);
        $zipLength = \strlen($transformed);

        if ($zipLength > $limit) {
            return \substr($transformed, 0, $limit);
        }

        if (!\is_null($type) && $zipLength < $limit) {
            return \str_pad($transformed, $limit, '0', $type);
        }

        return $transformed;
    }
}
