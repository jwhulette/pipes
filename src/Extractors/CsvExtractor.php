<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use SplFileObject;
use jwhulette\pipes\Frame;

class CsvExtractor implements ExtractorInterface
{
    protected Frame $frame;
    protected string $file;
    protected string $delimiter = ',';
    protected string $enclosure = '\'';
    protected int $skipHeaderLines = 1;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->frame = new Frame();
    }

    /**
     * Set the value of delimiter
     *
     * @return  CsvExtractor
     */
    public function setDelimiter(string $delimiter): CsvExtractor
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set the value of enclosure
     *
     * @return  CsvExtractor
     */
    public function setEnclosure(string $enclosure): CsvExtractor
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Set the value of skipHeaderLines
     *
     * @return  CsvExtractor
     */
    public function setSkipHeaderLines(int $skipHeaderLines): CsvExtractor
    {
        $this->skipHeaderLines = $skipHeaderLines;

        return $this;
    }

    /**
     * Extract the data from the source file.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $skip = 0;
        $file = new SplFileObject($this->file);
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $this->frame->setHeader(
            $file->fgetcsv($this->delimiter, $this->enclosure)
        );

        $file->rewind();

        while (!$file->eof()) {
            if ($skip < $this->skipHeaderLines) {
                ++$skip;

                $file->current();
            }

            $line = $file->fgetcsv($this->delimiter, $this->enclosure);
  
            if (!is_null($line)) {
                yield $this->frame->setData($line);
            }
        }

        $this->frame->setEnd();

        $file = null;
    }
}
