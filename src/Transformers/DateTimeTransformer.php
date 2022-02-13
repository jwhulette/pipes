<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use DateTime;
use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\DateTimeColumn;
use Jwhulette\Pipes\Frame;

/**
 * Change the date/time format of an item.
 */
class DateTimeTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected string $outputFormat = 'Y-m-d H:i:s';

    protected ?string $inputFormat = null;

    public function __construct()
    {
        $this->columns = new Collection();
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
        $this->columns->push(new DateTimeColumn(
            $column,
            $outputFormat ?? $this->outputFormat,
            $inputFormat ?? $this->inputFormat,
        ));

        return $this;
    }

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

    private function transformDateTime(string $datetime, DateTimeColumn $transform): string
    {
        if ($transform->inputFormat === null) {
            return (new DateTime($datetime))
                ->format($transform->outputFormat);
        }

        $dateObject = DateTime::createFromFormat($transform->inputFormat, $datetime);
        if ($dateObject === \false) {
            return '';
        }

        return $dateObject->format($transform->outputFormat);
    }
}
