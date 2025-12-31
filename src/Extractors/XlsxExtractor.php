<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use DateInterval;
use DateTimeInterface;
use Exception;
use Generator;
use function is_null;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\RowIterator;
use OpenSpout\Reader\XLSX\Sheet;
use Throwable;

final class XlsxExtractor implements ExtractorInterface
{
    protected Options $options;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

    protected Frame $frame;

    protected int $sheetIndex = 0;

    public function __construct(protected string $file)
    {
        $this->frame = new Frame();

        $this->options = new Options();
    }

    /**
     * Will return formatted dates.
     */
    public function formatDates(): self
    {
        $this->options->SHOULD_FORMAT_DATES = true;

        return $this;
    }

    /**
     * Skips empty rows and only return rows containing data.
     */
    public function preserveEmptyRows(): self
    {
        $this->options->SHOULD_PRESERVE_EMPTY_ROWS = true;

        return $this;
    }

    /**
     * Set to use 1904 dates.
     *
     * @see https://learn.microsoft.com/en-us/office/troubleshoot/excel/1900-and-1904-date-system
     */
    public function use19O4Dates(): self
    {
        $this->options->SHOULD_USE_1904_DATES = true;

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
     *
     * @throws IOException|ReaderNotOpenedException
     */
    public function extract(): Generator
    {
        $reader = new Reader($this->options);

        $reader->open($this->file);

        $selectedSheet = null;

        foreach ($reader->getSheetIterator() as $sheet) {
            /** @var Sheet $sheet */
            if ($sheet->getIndex() !== $this->sheetIndex) {
                continue;
            }

            $selectedSheet = $sheet;
        }

        return $this->readSheet($reader, $selectedSheet);
    }

    /**
     * @throws Throwable
     */
    private function readSheet(Reader $reader, ?Sheet $sheet): Generator
    {
        throw_if(
            is_null($sheet),
            Exception::class,
            'Unable to find selected sheet'
        );

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

            /** @var list<Cell> $cells */
            $cells = $row->getCells();

            yield $this->frame->setData(
                $this->makeRow($cells)
            );
        }

        $this->frame->setEnd();

        $reader->close();
    }

    /**
     * The use of rewind is needed when using current.
     *
     * @throws IOException
     */
    private function setHeader(RowIterator $rowIterator): void
    {
        $rowIterator->rewind();

        $row = $rowIterator->current();

        /** @var list<Cell> $cells */
        $cells = $row->getCells();

        $this->frame->setHeader(
            $this->makeRow(
                $cells
            )
        );
    }

    /**
     * @param  list<Cell>  $cells
     *
     * @return list<bool|float|int|string|null>
     */
    public function makeRow(array $cells): array
    {
        $array = [];

        foreach ($cells as $cell) {
            $cellValue = $cell->getValue();

            if ($cellValue instanceof DateTimeInterface) {
                $cellValue = $cellValue->format('Y-m-d H:i:s');
            }

            if ($cellValue instanceof DateInterval) {
                $cellValue = $cellValue->format('Y-m-d H:i:s');
            }

            $array[] = $cellValue;
        }

        return $array;
    }
}
