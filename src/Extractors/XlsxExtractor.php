<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Reader\XLSX\RowIterator;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class XlsxExtractor implements ExtractorInterface
{
    protected ReaderInterface $reader;
    protected int $skipLines = 0;
    protected bool $hasHeader = true;
    protected Frame $frame;
    protected int $sheetIndex = 0;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->reader = ReaderEntityFactory::createXLSXReader();

        $this->reader->setShouldFormatDates(true);

        $this->reader->open($file);

        $this->frame = new Frame();
    }

    /**
     * @return  XlsxExtractor
     */
    public function setNoHeader(): XlsxExtractor
    {
        $this->hasHeader = false;

        return $this;
    }

    /**
     * @param int $skipLines
     *
     * @return XlsxExtractor
     */
    public function setskipLines(int $skipLines): XlsxExtractor
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    /**
     * @param int $sheet
     *
     * @return XlsxExtractor
     */
    public function setSheetIndex(int $sheetIndex): XlsxExtractor
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }

    /**
     * @return Generator
     */
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

        $this->reader->close();
    }

    /**
     * The use of rewind is needed when using current.
     *
     * @see https://github.com/box/spout/pull/606#issuecomment-443745187
     *
     * @param RowIterator  $rowIterator
     */
    private function setHeader(RowIterator $rowIterator): void
    {
        $rowIterator->rewind();

        $row = $rowIterator->current();

        $this->frame->setHeader(
            $this->makeRow(
                $row->getCells()
            )
        );
    }

    /**
     * @param array $cells
     *
     * @return array
     */
    public function makeRow(array $cells): array
    {
        $collection = [];

        foreach ($cells as $cell) {
            $collection[] = (string) $cell->getValue();
        }

        return $collection;
    }
}
