<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use Illuminate\Support\Collection;

class Frame
{
    /**
     * @var \Illuminate\Support\Collection<int,string|int>
     */
    public Collection $header;

    /**
     * @var \Illuminate\Support\Collection<string|int,mixed|string>
     */
    public Collection $data;

    /**
     * @var array<int|string,string>
     */
    public array $attribute;

    public bool $end = false;

    /**
     * @param array<string|int,mixed|string>  $data
     *
     * @return Frame
     */
    public function setData(array $data): self
    {
        $this->data = collect($data);

        if (isset($this->header) && $this->header->isNotEmpty()) {
            $this->data = $this->header->combine($this->data);
        }

        return $this;
    }

    /**
     * @param array<int,string|int> $header
     */
    public function setHeader(array $header): void
    {
        $this->header = collect($header);
    }

    /**
     * @param array<int,string> $attribute
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
