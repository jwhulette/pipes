<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use DateInterval;
use DateTimeInterface;
use Generator;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\RowIterator;

class XlsxExtractor extends Extractor implements ExtractorInterface
{
    protected Reader $reader;

    protected bool $hasHeader = \true;

    protected int $sheetIndex = 0;

    public function __construct(string $file)
    {
        $options = new Options();
        $options->SHOULD_FORMAT_DATES = \true;

        $this->reader = new Reader($options);

        $this->reader->open($file);

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
            // Read the selected sheet
            if ($sheet->getIndex() === $this->sheetIndex) {
                $rowIterator = $sheet->getRowIterator();

                if ($this->hasHeader) {
                    $this->setHeader($rowIterator);
                    /*
                     * Since foreach resets the point to the beginning
                     * skip the header when looping the rows
                     */
                    $this->skipLines += 1;
                }

                foreach ($rowIterator as $row) {
                    if ($skip < $this->skipLines) {
                        $skip++;

                        continue;
                    }

                    if (! $row instanceof Row) {
                        return;
                    }

                    $cells = $row->getCells();
                    yield $this->frame->setData(
                        $this->makeRow($cells)
                    );
                }
            }
        }

        $this->frame->setEnd();

        $this->reader->close();
    }

    /**
     * The use of rewind is needed when using current.
     *
     * @see https://github.com/box/spout/pull/606#issuecomment-443745187
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
     * @param array<Cell> $cells
     *
     * @return array<int,bool|DateInterval|DateTimeInterface|float|int|string|null>
     */
    public function makeRow(array $cells): array
    {
        $array = [];

        foreach ($cells as $cell) {
            $array[] = $cell->getValue();
        }

        return $array;
    }
}
