<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Traits\CsvOptions;

class CsvExtractor extends Extractor implements ExtractorInterface
{
    use CsvOptions;
    
    protected string $file;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        $this->frame = new Frame();
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
     * @param string $escape
     *
     * @return  CsvExtractor
     */
    public function setEscape(string $escape): CsvExtractor
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @param int $skipLines
     *
     * @return  CsvExtractor
     */
    public function setSkipLines(int $skipLines): CsvExtractor
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
        $reader = Reader::createFromPath($this->file, 'r');
        $reader->setDelimiter($this->delimiter);
        $reader->setEnclosure($this->enclosure);
        $reader->setEscape($this->escape);

        if ($this->hasHeader === \true) {
            $reader->setHeaderOffset(0);
        }

        if ($this->hasHeader === \true) {
            try {
                $header = $reader->getHeader();

                $this->frame->setHeader($header);
            } catch (SyntaxError $exception) {
                $duplicateColumns = collect($exception->duplicateColumnNames())->implode(',');

                throw new PipesException('Duplicate column names '.$duplicateColumns, 1);
            }
        }

        $records = $reader->getRecords();
        foreach ($records as $offset => $record) {
            if ($offset < $this->skipLines) {
                continue;
            }

            yield $this->frame->setData($record);
        }

        $this->frame->setEnd();

        yield $this->frame;
    }
}
