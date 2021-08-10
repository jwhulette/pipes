<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Loaders;

use League\Csv\Writer;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Contracts\LoaderInterface;

/**
 * Write a csv file.
 */
class CsvLoader implements LoaderInterface
{
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';
    protected string $newline = '\n';
    protected Writer $writer;

    /**
     * @param string $ouputfile
     */
    public function __construct(string $ouputfile)
    {
        $this->writer = Writer::createFromPath($ouputfile, 'w+');
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
     * @param string $escape
     *
     * @return CsvLoader
     */
    public function setEscape(string $escape): CsvLoader
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @param string $newline
     *
     * @return CsvLoader
     */
    public function setNewline(string $newline): CsvLoader
    {
        $this->newline = $newline;

        return $this;
    }

    /**
     * @param Frame $frame
     */
    public function load(Frame $frame): void
    {
        $this->writer->setEscape($this->escape);
        $this->writer->setEnclosure($this->enclosure);
        $this->writer->setDelimiter($this->delimiter);
        $this->writer->setNewline($this->newline);

        $this->writer->insertOne($frame->data->values()->toArray());
    }
}
