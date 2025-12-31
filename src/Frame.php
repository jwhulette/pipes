<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Illuminate\Support\Collection;

final class Frame
{
    /**
     * @var Collection<int,string|int|float|bool|null>
     */
    public Collection $header;

    /**
     * @var Collection<int,string|int|float|bool|null>
     */
    public Collection $data;

    /**
     * @var array<int,string|int|float|bool|null>
     */
    public array $attributes;

    public bool $end = false;

    /**
     * Get the frame data.
     *
     * @return Collection<int,string|int|float|bool|null>
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param  list<string|int|float|bool|null>  $data
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
     * Get the frame header.
     *
     * @return Collection<int,string|int|float|bool|null>
     */
    public function getHeader(): Collection
    {
        return $this->header;
    }

    /**
     * Set the frame header data.
     *
     * @param  list<string|int|float|bool|null>  $header
     */
    public function setHeader(array $header): void
    {
        $this->header = collect($header);
    }

    /**
     * Set a frame attribute.
     *
     * @param  array<int,string|int|float|bool|null>  $attribute
     */
    public function setAttribute(array $attribute): void
    {
        $key = key($attribute);

        if (is_null($key)) {
            return;
        }

        $this->attributes[$key] = $attribute[$key];
    }

    /**
     * Get all the frame attributes.
     *
     * @return array<int,string|int|float|bool|null>
     */
    public function getAllAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get single frame attribute.
     */
    public function getAttribute(string $key): string|int|float|bool|null
    {
        return $this->attributes[$key];
    }

    /**
     * Get the frame the end frame value.
     */
    public function getEnd(): bool
    {
        return $this->end;
    }

    /**
     * Set the frame as the last data element.
     */
    public function setEnd(): void
    {
        $this->end = true;
    }
}
