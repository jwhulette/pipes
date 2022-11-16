<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\RowIterator;

class XlsxExtractor implements ExtractorInterface
{
    protected ReaderInterface $reader;

    protected string $file;

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

    protected Frame $frame;

    protected int $sheetIndex = 0;

    public function __construct(string $file)
    {
        $this->reader = ReaderFactory::createFromFile($file);

        $this->frame = new Frame();
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
        $skip = 0;

        foreach ($this->reader->getSheetIterator() as $sheet) {
            /** @var \OpenSpout\Reader\XLSX\Sheet $sheet */
            if ($sheet->getIndex() === $this->sheetIndex) {
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
        }

        $this->frame->setEnd();
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
     * @return array<int,string>
     */
    public function makeRow(array $cells): array
    {
        $collection = [];

        foreach ($cells as $cell) {
            $cellValue = $cell->getValue();
            $collection[] = \strval($cellValue);
        }

        return $collection;
    }
}
