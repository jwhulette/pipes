<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Exceptions\PipesException;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Traits\CsvOptions;
use League\Csv\Reader;
use League\Csv\SyntaxError;

class CsvExtractor extends Extractor implements ExtractorInterface
{
    use CsvOptions;

    protected string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->frame = new Frame();
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function setEscape(string $escape): self
    {
        $this->escape = $escape;

        return $this;
    }

    public function setSkipLines(int $skipLines): self
    {
        $this->skipLines = $skipLines;

        return $this;
    }

    public function setNoHeader(): self
    {
        $this->hasHeader = false;

        return $this;
    }

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
