<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Exceptions\PipesException;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Traits\CsvOptions;
use SplFileObject;

class FixedWithExtractor extends Extractor implements ExtractorInterface
{
    use CsvOptions;
    
    protected array $columnWidths = [];

    protected bool $allColumns = false;

    protected int $width;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        $this->frame = new Frame();
    }

    /**
     * @param int $width
     *
     * @return FixedWithExtractor
     */
    public function setAllColumns(int $width): FixedWithExtractor
    {
        $this->allColumns = true;

        $this->width = $width;

        return $this;
    }

    /**
     * Set the column with.
     *
     * @param array $widths
     *
     * @return FixedWithExtractor
     */
    public function setColumnsWidth(array $widths): FixedWithExtractor
    {
        $this->columnWidths = $widths;

        return $this;
    }

    /**
     * @param int $skipLines
     *
     * @return FixedWithExtractor
     */
    public function setSkipLines(int $skipLines): FixedWithExtractor
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    /**
     * @return  FixedWithExtractor
     */
    public function setNoHeader(): FixedWithExtractor
    {
        $this->hasHeader = false;

        return $this;
    }

    /**
     * @return Generator
     */
    public function extract(): Generator
    {
        $file = new SplFileObject($this->file);

        $file->setFlags(SplFileObject::READ_AHEAD);

        if ($this->hasHeader) {
            $line = $file->fgets();

            if ($line === false) {
                throw new PipesException('Error reading file!');
            }

            $this->frame->setHeader(
                $this->makeFrame(
                    trim($line)
                )
            );

            // Go back to the begining of the file
            $file->rewind();
        }

        // Skip the number of lines minus one as it's a zero based index
        if ($this->skipLines > 0) {
            $file->seek($this->skipLines - 1);
        }

        while (! $file->eof()) {
            $line = $file->fgets();

            if ($line === false) {
                throw new PipesException('Error reading file!');
            }

            yield $this->frame->setData(
                $this->makeFrame(
                    trim($line)
                )
            );
        }

        $this->frame->setEnd();

        $file = null;
    }

    /**
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

        $offset = 0;

        $widths = $this->columnWidths;

        foreach ($widths as $width) {
            $item = substr($row, (int) $offset, $width);

            $data[] = trim($item);

            // Reset the ranges
            $offset += $width;
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

        $offset = 0;

        while ($length >= $offset) {
            $item = substr($row, $offset, $this->width);

            $data[] = trim($item);

            // Reset the ranges
            $offset += $this->width;
        }

        return $data;
    }
}
