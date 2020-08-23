<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use SplFileObject;
use jwhulette\pipes\Frame;

/**
 * Write a csv file.
 */
class CsvLoader implements LoaderInterface
{
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escapeCharacter = '\\';
    protected SplFileObject $file;

    /**
     * @param string $ouputfile
     */
    public function __construct(string $ouputfile)
    {
        $this->file = new SplFileObject($ouputfile, 'w');
    }

    /**
     * @param string $delimiter
     *
     * @return CsvLoader
     */
    public function setDelimiter(string $delimiter): CsvLoader
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @param string $enclosure
     *
     * @return CsvLoader
     */
    public function setEnclosure(string $enclosure): CsvLoader
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @param string $escapeCharacter
     *
     * @return CsvLoader
     */
    public function setEscapeCharacter(string $escapeCharacter): CsvLoader
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    /**
     * @param Frame $frame
     */
    public function load(Frame $frame): void
    {
        $this->file->fputcsv(
            $frame->data->values()->toArray(),
            $this->delimiter,
            $this->enclosure,
            $this->escapeCharacter
        );

        // Close the file
        if ($frame->end === true) {
            unset($this->file);
        }
    }
}
