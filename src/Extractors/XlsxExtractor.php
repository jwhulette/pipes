<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Exception;
use Generator;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\RowIterator;
use OpenSpout\Reader\XLSX\Sheet;

final class XlsxExtractor implements ExtractorInterface
{
    protected string $file;

    protected Options $options;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

    protected Frame $frame;

    protected int $sheetIndex = 0;

    public function __construct(string $file)
    {
        $this->file = $file;

        $this->frame = new Frame();

        $this->options = new Options();
    }

    /**
     * Will return formatted dates.
     */
    public function formatDates(): self
    {
        $this->options->SHOULD_FORMAT_DATES = \true;

        return $this;
    }

    /**
     * Skips empty rows and only return rows containing data.
     */
    public function preserveEmptyRows(): self
    {
        $this->options->SHOULD_PRESERVE_EMPTY_ROWS = \true;

        return $this;
    }

    /**
     * Set to use 1904 dates.
     *
     * @see https://learn.microsoft.com/en-us/office/troubleshoot/excel/1900-and-1904-date-system
     */
    public function use19O4Dates(): self
    {
        $this->options->SHOULD_USE_1904_DATES = \true;

        return $this;
    }

    /**
     * The file does not have a header row.
     */
    public function setNoHeader(): self
    {
        $this->hasHeader = false;

        return $this;
    }

    /**
     * The number of lines to skip at the beginning of the file.
     */
    public function setSkipLines(int $skipLines): self
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    /**
     * Set the sheet to read
     * Zero based index, ie. first sheet is 0.
     */
    public function setSheetIndex(int $sheetIndex): self
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }

    /**
     * Extract the data from the file.
     */
    public function extract(): Generator
    {
        $reader = new Reader($this->options);
        $reader->open($this->file);
        $selectedSheet = \null;

        foreach ($reader->getSheetIterator() as $sheet) {
            /** @var \OpenSpout\Reader\XLSX\Sheet $sheet */
            if ($sheet->getIndex() !== $this->sheetIndex) {
                continue;
            }

            $selectedSheet = $sheet;
        }

        return $this->readSheet($reader, $selectedSheet);
    }

    /**
     * @param array<int,\OpenSpout\Common\Entity\Cell> $cells
     *
     * @return array<int,mixed>
     */
    public function makeRow(array $cells): array
    {
        $array = [];

        foreach ($cells as $cell) {
            $array[] = $cell->getValue();
        }

        return $array;
    }

    private function readSheet(Reader $reader, ?Sheet $sheet): Generator
    {
        if (\is_null($sheet)) {
            throw new Exception('Unable to find selected sheet', 1);
        }

        $rowIterator = $sheet->getRowIterator();

        if ($this->hasHeader) {
            $this->setHeader($rowIterator);
            /*
             * Since foreach resets the point to the beginning
             * skip the header when looping the rows
             */
            $this->skipLines += 1;
        }

        $skip = 0;
        foreach ($rowIterator as $row) {
            if ($skip < $this->skipLines) {
                $skip++;

                continue;
            }

            yield $this->frame->setData(
                $this->makeRow($row->getCells())
            );
        }

        $this->frame->setEnd();

        $reader->close();
    }

    /**
     * The use of rewind is needed when using current.
     */
    private function setHeader(RowIterator $rowIterator): void
    {
        $rowIterator->rewind();

        $row = $rowIterator->current();

        if (! $row instanceof Row) {
            return;
        }

        $this->frame->setHeader(
            $this->makeRow(
                $row->getCells()
            )
        );
    }
}
