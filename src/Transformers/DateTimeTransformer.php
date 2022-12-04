<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Carbon\Carbon;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\DateTimeDto;
use Jwhulette\Pipes\Frame;

final class DateTimeTransformer implements TransformerInterface
{
    /**
     * @var array<int,DateTimeDto>
     */
    protected array $columns;

    /**
     * Set the columns and transformation.
     *
     * @param string|int $column
     * @param string|null $outputFormat
     * @param string|null $inputFormat
     *
     * @return self
     */
    public function transformColumn(string|int $column, ?string $outputFormat = null, ?string $inputFormat = null): self
    {
        $this->columns[] = new DateTimeDto($column, $outputFormat, $inputFormat);

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

    private function transformDateTime(string $datetime, DateTimeDto $dateTimeDto): string
    {
        if (is_null($dateTimeDto->inputFormat)) {
            $dateTime = new Carbon($datetime);

            return $this->format($dateTime, $dateTimeDto);
        }

        try {
            $dateTime = Carbon::createFromFormat($dateTimeDto->inputFormat, $datetime);
        } catch (\Throwable $th) {
            throw new \Exception('Unable to create date object, error: ' . $th->getMessage(), 1);
        }

        if ($dateTime === \false) {
            throw new \Exception('Unable to create date object from string', 1);
        }

        return $this->format($dateTime, $dateTimeDto);
    }

    private function format(Carbon $dateTime, DateTimeDto $dateTimeDto): string
    {
        if (! is_null($dateTimeDto->outputFormat)) {
            return $dateTime->format($dateTimeDto->outputFormat);
        }

        return $dateTime->toDateTimeString();
    }
}
