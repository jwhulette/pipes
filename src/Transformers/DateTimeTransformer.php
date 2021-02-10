<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Frame;
use Illuminate\Support\Collection;

/**
 * Change the date/time format of an item.
 */
class DateTimeTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected string $outputFormat = 'Y-m-d H:i:s';

    protected ?string $inputFormat = null;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->columns = new Collection;
    }

    /**
     * @param mixed $column name|index
     * @param string|null $outputFormat
     * @param string|null $inputFormat
     *
     * @return DateTimeTransformer
     */
    public function transformColumn(
        $column,
        ?string $outputFormat = null,
        ?string $inputFormat = null
    ): DateTimeTransformer {
        $this->columns->push((object) [
            'column' => $column,
            'outputFormat' => $outputFormat ?? $this->outputFormat,
            'inputFormat' => $inputFormat ?? $this->inputFormat,
        ]);

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
                if ($column->column === $key) {
                    return $this->transformDateTime($item, $column);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $datetime
     * @param object $transform
     *
     * @return string
     */
    private function transformDateTime(string $datetime, object $transform): string
    {
        if ($transform->inputFormat === null) {
            return (new DateTime($datetime))
                ->format($transform->outputFormat);
        }

        return (DateTime::createFromFormat($transform->inputFormat, $datetime))
            ->format($transform->outputFormat);
    }
}
