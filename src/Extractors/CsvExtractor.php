<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use SplFileObject;
use jwhulette\pipes\Frame;

class CsvExtractor extends Extractor implements ExtractorInterface
{
    protected string $delimiter = ',';

    protected string $enclosure = '\'';

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        $this->frame = new Frame;
    }

    /**
     * @param string $delimiter
     *
     * @return  CsvExtractor
     */
    public function setDelimiter(string $delimiter): CsvExtractor
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @param string $enclosure
     *
     * @return  CsvExtractor
     */
    public function setEnclosure(string $enclosure): CsvExtractor
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @param int $skipLines
     *
     * @return  CsvExtractor
     */
    public function setskipLines(int $skipLines): CsvExtractor
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    /**
     * @return  CsvExtractor
     */
    public function setNoHeader(): CsvExtractor
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
        $file = new SplFileObject($this->file);

        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        if ($this->hasHeader) {
            $this->frame->setHeader(
                $file->fgetcsv($this->delimiter, $this->enclosure)
            );

            // Go back to the begining of the file
            $file->rewind();
        }

        // Skip the number of lines minus one as it's a zero based index
        if ($this->skipLines > 0) {
            $file->seek($this->skipLines - 1);
        }

        while (!$file->eof()) {
            $line = $file->fgetcsv($this->delimiter, $this->enclosure);

            if (!is_null($line)) {
                yield $this->frame->setData($line);
            }
        }

        $this->frame->setEnd();

        $file = null;
    }
}
