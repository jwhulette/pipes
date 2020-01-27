<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Frame;

class DateTimeTransformer implements TransformerInterface
{
    protected array $columns = [];

    /**
     * Set the column to transform
     *
     * @param string $column
     * @param string $outputFormat
     * @param string $inputFormat
     *
     * @return DateTimeTransformer
     */
    public function transformColumn(string $column, string $outputFormat = 'Y-m-d', string $inputFormat = ''): DateTimeTransformer
    {
        $this->columns[] = [
            'column' => (is_numeric($column) ? (int) $column : $column),
            'outputFormat' => $outputFormat,
            'inputFormat' => $inputFormat
        ];

        return $this;
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
                    return $this->transformDateTime($item, $column);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * Format the date.
     *
     * @param string $datetime
     * @param array $transform
     *
     * @return string
     */
    private function transformDateTime(string $datetime, array $transform): string
    {
        if ($transform['inputFormat'] === '') {
            $date = new DateTime($datetime);
            return $date->format($transform['outputFormat']);
        }

        $dateObject = DateTime::createFromFormat($transform['inputFormat'], $datetime);
        return $dateObject->format($transform['outputFormat']);
    }
}
