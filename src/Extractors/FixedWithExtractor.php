<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use SplFileObject;
use jwhulette\pipes\Frame;

class FixedWithExtractor implements ExtractorInterface
{
    /** @var string */
    protected $file;

    /** @var int */
    protected $skipHeaderLines = 1;

    /** @var array */
    protected $columnWidths;

    /** @var bool */
    protected $allColumns = false;

    /** @var int */
    protected $width;

    /** @var \jwhulette\pipes\Frame */
    protected $frame;

    /**
     * Construct.
     *
     * @param string $file
     * @param array  $columnWidths
     * @param int    $skipHeaderLines
     */
    public function __construct(string $file, array $columnWidths, int $skipHeaderLines = null)
    {
        $this->file = $file;

        $this->frame = new Frame;

        $this->columnWidths = $columnWidths;

        if (!is_null($skipHeaderLines)) {
            $this->skipHeaderLines = $skipHeaderLines;
        }

        // Apply to all columns
        if (key($this->columnWidths) === '*') {
            $this->allColumns = true;

            $this->width = $this->columnWidths['*'];
        }
    }

    /**
     * Extract the data from the source file.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $skip = 0;

        $file = new SplFileObject($this->file);

        $this->frame->setHeader(
            $this->makeFrame(
                trim($file->fgets())
            )
        );

        $file->rewind();

        while (!$file->eof()) {

            if ($skip < $this->skipHeaderLines) {
                ++$skip;

                $file->current();
            }

            yield $this->frame->setData(
                $this->makeFrame(
                    trim($file->fgets())
                )            
            );
        }

        $this->end();
        yield $this->frame;

        $file = null;
    }


    /**
     * Set the extractor end flag
     */
    public function end(): void
    {
        $this->frame->setEnd();
    }    

    /**
     * Convert the data to an array
     *
     * @param string $row
     *
     * @return array
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
     * @return array
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
     * @return array
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
