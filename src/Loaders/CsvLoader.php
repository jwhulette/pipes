<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Loaders;

use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Traits\CsvOptions;
use League\Csv\Writer;

/**
 * Write a csv file.
 */
class CsvLoader implements LoaderInterface
{
    use CsvOptions;

    protected Writer $writer;

    public function __construct(string $ouputfile)
    {
        $this->writer = Writer::createFromPath($ouputfile, 'w+');
    }

    public function setDelimiter(string $delimiter): CsvLoader
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): CsvLoader
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function setEscape(string $escape): CsvLoader
    {
        $this->escape = $escape;

        return $this;
    }

    public function setNewline(string $newline): CsvLoader
    {
        $this->newline = $newline;

        return $this;
    }

    public function load(Frame $frame): void
    {
        $this->writer->setEscape($this->escape);
        $this->writer->setEnclosure($this->enclosure);
        $this->writer->setDelimiter($this->delimiter);
        $this->writer->setNewline($this->newline);

        $this->writer->insertOne($frame->data->values()->toArray());
    }
}
