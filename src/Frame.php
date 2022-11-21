<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Illuminate\Support\Collection;

final class Frame
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
    public array $attributes;

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
     * @return \Illuminate\Support\Collection<int,mixed>
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param array<int,mixed> $header
     */
    public function setHeader(array $header): void
    {
        $this->header = collect($header);
    }

    /**
     * @return \Illuminate\Support\Collection<int,mixed>
     */
    public function getHeader(): Collection
    {
        return $this->header;
    }

    /**
     * @param array<int,string> $attribute
     */
    public function setAttribute(array $attribute): void
    {
        $this->attributes[key($attribute)] = $attribute[key($attribute)];
    }

    /**
     * @return array<int|string,string>
     */
    public function getAllAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key];
    }

    /**
     * Set the frame as the last data element.
     */
    public function setEnd(): void
    {
        $this->end = true;
    }

    public function getEnd(): bool
    {
        return $this->end;
    }
}
