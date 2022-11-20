<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Illuminate\Support\Collection;

class Frame
{
    /**
     * @var \Illuminate\Support\Collection<int,mixed>
     */
    public Collection $header;

    /**
     * @var \Illuminate\Support\Collection<int,mixed>
     */
    public Collection $data;

    /**
     * @var array<int|string,string>
     */
    public array $attribute;

    public bool $end = false;

    /**
     * @param array<int,mixed>  $data
     *
     * @return Frame
     */
    public function setData(array $data): self
    {
        $this->data = collect($data);

        if (isset($this->header) && $this->header->isNotEmpty()) {
            // @phpstan-ignore-next-line
            $this->data = $this->header->combine($this->data);
        }

        return $this;
    }

    /**
     * @param array<int,mixed> $header
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
