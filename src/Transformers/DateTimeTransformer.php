<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Dto\DateTimeDto;
use jwhulette\pipes\Frame;

class DateTimeTransformer implements TransformerInterface
{
    /**
     * @var array<int,\jwhulette\pipes\Dto\DateTimeDto>
     */
    protected array $columns;

    protected string $outputFormat = 'Y-m-d';

    protected string $inputFormat = '';

    public function transformColumn(string|int $column, ?string $outputFormat = null, ?string $inputFormat = null): self
    {
        $lineOutputFormat = $outputFormat ?? $this->outputFormat;
        $lineInputFormat = $inputFormat ?? $this->inputFormat;

        $this->columns[] = new DateTimeDto($column, $lineOutputFormat, $lineInputFormat);

        return $this;
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->data->transform(function ($item, $key) {
            foreach ($this->columns as $dto) {
                if ($dto->column === $key) {
                    return $this->transformDateTime(\strval($item), $dto);
                }
            }

            return $item;
        });

        return $frame;
    }

    /**
     * @param string $datetime
     * @param DateTimeDto $dateTimeDto
     *
     * @return string
     */
    private function transformDateTime(string $datetime, DateTimeDto $dateTimeDto): string
    {
        if ($dateTimeDto->inputFormat === '') {
            $date = new DateTime($datetime);

            return $date->format($dateTimeDto->outputFormat);
        }

        $dateObject = DateTime::createFromFormat($dateTimeDto->inputFormat, $datetime);

        if ($dateObject === \false) {
            throw new \Exception('Unable to create date object from string', 1);
        }

        return $dateObject->format($dateTimeDto->outputFormat);
    }
}
