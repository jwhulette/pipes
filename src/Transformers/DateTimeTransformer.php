<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Frame;

class DateTimeTransformer implements TransformerInterface
{
    /**
     * @var array<int,array<string,int|string>>
     */
    protected array $columns;

    protected string $outputFormat = 'Y-m-d';

    protected string $inputFormat = '';

    public function transformColumn(string $column, ?string $outputFormat = null, ?string $inputFormat = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'outputFormat' => $outputFormat ?? $this->outputFormat,
            'inputFormat' => $inputFormat ?? $this->inputFormat,
        ];

        return $this;
    }

    public function transformColumnByIndex(int $column, ?string $outputFormat = null, ?string $inputFormat = null): self
    {
        $this->columns[] = [
            'column' => $column,
            'outputFormat' => $outputFormat ?? $this->outputFormat,
            'inputFormat' => $inputFormat ?? $this->inputFormat,
        ];

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                /** @var string $key */
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
     * @param array<int|string,int|string> $transform
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
