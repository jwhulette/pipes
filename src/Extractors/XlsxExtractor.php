<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Iterator;
use Generator;
use jwhulette\pipes\Frame;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class XlsxExtractor implements ExtractorInterface
{
    protected ReaderInterface $reader;
    protected int $skipHeaderLines = 1;
    protected Frame $frame;

    /**
     * XlsxExtrator.
     *
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
     * Set the value of skipHeaderLines
     *
     * @return  XlsxExtractor
     */
    public function setSkipHeaderLines(int $skipHeaderLines): XlsxExtractor
    {
        $this->skipHeaderLines = $skipHeaderLines;

        return $this;
    }
    
    /**
     * Get a file line.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $skip = 0;
        $sheet = $this->reader->getSheetIterator();
        $this->setHeader($sheet);

        foreach ($this->reader->getSheetIterator() as $sheet) {
            // Read only first sheet
            if ($sheet->getIndex() === 0) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($skip < $this->skipHeaderLines) {
                        ++$skip;
                        continue;
                    }

                    yield $this->frame->setData(
                        $this->makeRow(
                            $row->getCells()
                        )
                    );
                }

                break;
            }
        }

        $this->frame->setEnd();

        $this->reader->close();
    }

    /**
     * Set the frame header.
     *
     * The use of rewind is needed when using current
     *
     * @see https://github.com/box/spout/pull/606#issuecomment-443745187
     *
     * @param Iterator $sheet
     */
    private function setHeader($sheet): void
    {
        $sheet->rewind();
        $currentSheet = $sheet->current();
        $readerIterator = $currentSheet->getRowIterator();
        $readerIterator->rewind();
        $row = $readerIterator->current();
        $this->frame->setHeader(
            $this->makeRow(
                $row->getCells()
            )
        );

        $sheet->rewind();
        $readerIterator->rewind();
    }

    /**
     * Create a new frame row from the data.
     *
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
