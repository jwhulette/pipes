<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Reader\XLSX\RowIterator;
use Generator;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Traits\CsvOptions;

class XlsxExtractor extends Extractor implements ExtractorInterface
{
    use CsvOptions;

    protected ReaderInterface $reader;

    protected int $sheetIndex = 0;

    public function __construct(string $file)
    {
        $this->reader = ReaderEntityFactory::createXLSXReader();
        $this->reader->setShouldFormatDates(true);
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

                    yield $this->frame->setData(
                        $this->makeRow($row->getCells())
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

        if (\is_null($row)) {
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
     * @return array<string>
     */
    public function makeRow(array $cells): array
    {
        $array = [];

        foreach ($cells as $cell) {
            $array[] = (string) $cell->getValue();
        }

        return $array;
    }
}
