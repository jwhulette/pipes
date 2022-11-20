<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
// use SplFileObject;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader;
use OpenSpout\Reader\CSV\RowIterator;
use OpenSpout\Reader\CSV\Sheet;

class CsvExtractor implements ExtractorInterface
{
    protected Frame $frame;

    protected string $file;

    protected Options $options;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->frame = new Frame();
        $this->options = new Options();
    }

    public function preserveEmptyRows(bool $preserve): self
    {
        $this->options->SHOULD_PRESERVE_EMPTY_ROWS = $preserve;

        return $this;
    }

    public function setEncoding(string $encoding): self
    {
        $this->options->ENCODING = $encoding;

        return $this;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->options->FIELD_DELIMITER = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->options->FIELD_ENCLOSURE = $enclosure;

        return $this;
    }

    public function setSkipLines(int $skipLines): self
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    public function setNoHeader(): self
    {
        $this->hasHeader = false;

        return $this;
    }

    /**
     * Extract the data from the source file.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $reader = new Reader($this->options);
        $reader->open($this->file);

        $sheet = $reader->getSheetIterator()->current();

        return $this->readSheet($sheet);

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
