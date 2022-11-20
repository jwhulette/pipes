<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Jwhulette\Pipes\Extractors\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\RowIterator;
use OpenSpout\Reader\XLSX\Sheet;

class XlsxExtractor implements ExtractorInterface
{
    protected string $file;

    protected Options $options;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

    protected Frame $frame;

    protected int $sheetIndex = 0;

    protected string $dateFormat = 'Y-m-d H:i:s';

    protected bool $shouldFormatDate = \false;

    public function __construct(string $file)
    {
        $this->file = $file;

        $this->frame = new Frame();

        $this->options = new Options();
    }

    public function formatDates(): self
    {
        $this->options->SHOULD_FORMAT_DATES = \true;

        return $this;
    }

    public function preserveEmptyRows(): self
    {
        $this->options->SHOULD_PRESERVE_EMPTY_ROWS = \true;

        return $this;
    }

    public function use19O4Dates(): self
    {
        $this->options->SHOULD_USE_1904_DATES = \true;

        return $this;
    }

    public function setNoHeader(): self
    {
        $this->hasHeader = false;

        return $this;
    }

    public function setSkipLines(int $skipLines): self
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    public function setSheetIndex(int $sheetIndex): self
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }

    public function extract(): Generator
    {
        $reader = new Reader($this->options);
        $reader->open($this->file);

        foreach ($reader->getSheetIterator() as $sheet) {
            /* @var \OpenSpout\Reader\XLSX\Sheet $sheet */
            if ($sheet->getIndex() !== $this->sheetIndex) {
                continue;
            }

            return $this->readSheet($sheet);
        }

        $this->frame->setEnd();

        $reader->close();
    }

    private function readSheet(Sheet $sheet): Generator
    {
        $skip = 0;
        $rowIterator = $sheet->getRowIterator();

        if ($this->hasHeader) {
            $this->setHeader($rowIterator);
            /*
             * Since foreach resets the point to the beginning
             * skip the header when looping the rows
             */
            $this->skipLines = $this->skipLines + 1;
        }

        foreach ($rowIterator as $row) {
            if ($skip < $this->skipLines) {
                $skip++;

                continue;
            }

            yield $this->frame->setData(
                $this->makeRow($row->getCells())
            );
        }
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

    /**
     * @param array<int,\OpenSpout\Common\Entity\Cell> $cells
     *
     * @return array<int,mixed>
     */
    public function makeRow(array $cells): array
    {
        $collection = [];

        foreach ($cells as $cell) {
            $collection[] = $cell->getValue();
        }

        return $collection;
    }
}
