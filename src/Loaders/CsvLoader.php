<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use SplFileObject;
use jwhulette\pipes\Frame;

class CsvLoader implements LoaderInterface
{
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escapeCharacter = '\\';
    protected SplFileObject $file;

    /**
     * CsvLoader.
     *
     * @param string $ouputfile
     */
    public function __construct(string $ouputfile)
    {
        $this->file = new SplFileObject($ouputfile, 'w');
    }

    /**
     * Set the value of delimiter.
     *
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
     * Set the value of enclosure.
     *
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
     * Set the value of escapeCharacter.
     *
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
     * Write the data to the file.
     *
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
