<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
use SplFileObject;

class FixedWithExtractor implements ExtractorInterface
{
    protected string $file;

    protected int $skipLines = 0;

    /**
     * @var array<int,int>
     */
    protected array $columnWidths;

    protected bool $allColumns = false;

    protected int $width;

    protected Frame $frame;

    protected bool $hasHeader = true;

    /**
     * @param string $file
     * @param array<int,int>  $columnWidths
     */
    public function __construct(string $file, array $columnWidths = [])
    {
        $this->file = $file;

        $this->frame = new Frame;

        $this->columnWidths = $columnWidths;
    }

    public function setSkipLines(int $skipLines): self
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    public function setAllColumns(int $width): self
    {
        $this->allColumns = true;

        $this->width = $width;

        return $this;
    }

    public function setNoHeader(): self
    {
        $this->hasHeader = false;

        return $this;
    }

    public function extract(): Generator
    {
        $file = new SplFileObject($this->file);

        if ($this->hasHeader) {
            $this->frame->setHeader(
                $this->makeFrame(
                    trim(strval($file->fgets()))
                )
            );

            // Go back to the beginning of the file
            $file->rewind();
        }

        // Skip the number of lines minus one as it's a zero based index
        if ($this->skipLines > 0) {
            $file->seek($this->skipLines);
        }

        while (! $file->eof()) {
            yield $this->frame->setData(
                $this->makeFrame(
                    trim(strval($file->fgets()))
                )
            );
        }

        $this->frame->setEnd();

        $file = null;
    }

    /**
     * @param string $row
     *
     * @return array<int,string>
     */
    private function makeFrame(string $row): array
    {
        // All columns are the same width
        if ($this->allColumns) {
            return $this->allColumnsEqual($row);
        }

        // Columns have different widths
        return $this->columnSizes($row);
    }

    /**
     * All columns are of equal.
     *
     * @param string $row
     *
     * @return array<int,string>
     */
    private function columnSizes(string $row): array
    {
        $data = [];

        $rangeStart = 0;

        $widths = $this->columnWidths;

        foreach ($widths as $width) {
            $item = substr($row, $rangeStart, $width);

            $data[] = trim($item);

            // Reset the ranges
            $rangeStart += $width;
        }

        return $data;
    }

    /**
     * All columns are of equal.
     *
     * @param string $row
     *
     * @return array<int,string>
     */
    private function allColumnsEqual(string $row): array
    {
        $data = [];

        $length = strlen($row);

        $rangeStart = 0;

        while ($length >= $rangeStart) {
            $item = substr($row, $rangeStart, $this->width);

            $data[] = trim($item);

            // Reset the ranges
            $rangeStart += $this->width;
        }

        return $data;
    }
}
