<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Iterator;
use Generator;
use jwhulette\pipes\Frame;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class XlsxExtractor implements ExtractorInterface
{
    /** @var \Box\Spout\Reader\ReaderInterface */
    protected $reader;

    /** @var int */
    protected $skipHeaderLines;

    /** @var \jwhulette\pipes\Frame */
    protected $frame;

    /**
     * XlsxExtrator.
     *
     * @param string $file
     * @param int    $skipHeaderLines
     */
    public function __construct(string $file, int $skipHeaderLines = 1)
    {
        $this->skipHeaderLines = $skipHeaderLines;

        $this->reader = ReaderEntityFactory::createXLSXReader();

        $this->reader->open($file);

        $this->reader->setShouldFormatDates(true);

        $this->reader->open($file);

        $this->frame = new Frame();
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

        $this->end();
        yield $this->frame;

        $this->reader->close();
    }

    /**
     * Set the extractor end flag.
     */
    public function end(): void
    {
        $this->frame->setEnd();
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
