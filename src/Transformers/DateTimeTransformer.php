<?php

declare(strict_types=1);

namespace jwhulette\pipes\Transformers;

use DateTime;
use jwhulette\pipes\Frame;

class DateTimeTransformer implements TransformerInterface
{
    /** @var array */
    protected $columns;

    /** @var string */
    protected $inputFormat;

    /** @var string */
    protected $outputFormat;

    /**
     * DateTimeTransformer.
     *
     * @param array  $columns
     * @param string $outputFormat
     * @param string $inputFormat
     */
    public function __construct(array $columns, string $outputFormat = 'Y-m-d', string $inputFormat = null)
    {
        $this->columns = $columns;

        $this->outputFormat = $outputFormat;

        $this->inputFormat = $inputFormat;
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
                return $this->transformDateTime($item);
            }

            return $item;
        });

        return $frame;
    }

    /**
     * Format the date.
     *
     * @param string $datetime
     *
     * @return string
     */
    private function transformDateTime(string $datetime): string
    {
        if ($this->inputFormat === null) {
            $date = new DateTime($datetime);

            return $date->format($this->outputFormat);
        }

        $dateObject = DateTime::createFromFormat($this->inputFormat, $datetime);

        return $dateObject->format($this->outputFormat);
    }
}
