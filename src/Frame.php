<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Illuminate\Support\Collection;

/**
 * A data frame.
 */
class Frame
{
    public Collection $header;

    public Collection $data;

    public array $attribute = [];

    public bool $end = false;

    /**
     * @param array $data
     *
     * @return Frame
     */
    public function setData(array $data): Frame
    {
        $this->data = collect($data);

        if (isset($this->header) && $this->header->isNotEmpty()) {
            $this->data = $this->header->combine($this->data);
        }

        return $this;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header): void
    {
        $this->header = collect($header);
    }

    /**
     * Set extra attributes to a data frame for use in processing
     * @param array $attribute
     */
    public function setAttribute(array $attribute): void
    {
        $this->attribute[key($attribute)] = $attribute[key($attribute)];
    }

    /**
     * Set the frame as the last data element.
     */
    public function setEnd(): void
    {
        $this->end = true;
    }
}
