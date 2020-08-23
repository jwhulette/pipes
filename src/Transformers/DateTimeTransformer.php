<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Frame;

/**
 * Change the date/time format of an item.
 */
class DateTimeTransformer implements TransformerInterface
{
    protected array $columns = [];
    protected string $outputFormat = 'Y-m-d H:i:s';
    protected ?string $inputFormat = null;

    /**
     * @param string $column
     * @param string|null $outputFormat
     * @param string|null $inputFormat
     *
     * @return DateTimeTransformer
     */
    public function transformColumn(
        string $column,
        ?string $outputFormat = null,
        ?string $inputFormat = null
    ): DateTimeTransformer {
        $this->columns[] = [
            'column' => $column,
            'outputFormat' => $outputFormat ?? $this->outputFormat,
            'inputFormat' => $inputFormat ?? $this->inputFormat,
        ];

        return $this;
    }

    /**
     * @param int $column
     * @param string|null $outputFormat
     * @param string|null $inputFormat
     *
     * @return DateTimeTransformer
     */
    public function transformColumnByIndex(
        int $column,
        ?string $outputFormat = null,
        ?string $inputFormat = null
    ): DateTimeTransformer {
        $this->columns[] = [
            'column' => $column,
            'outputFormat' => $outputFormat ?? $this->outputFormat,
            'inputFormat' => $inputFormat ?? $this->inputFormat,
        ];

        return $this;
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
                    return $this->transformDateTime($item, $column);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $datetime
     * @param array $transform
     *
     * @return string
     */
    private function transformDateTime(string $datetime, array $transform): string
    {
        if ($transform['inputFormat'] === null) {
            return (new DateTime($datetime))
                ->format($transform['outputFormat']);
        }

        return (DateTime::createFromFormat($transform['inputFormat'], $datetime))
            ->format($transform['outputFormat']);
    }
}
