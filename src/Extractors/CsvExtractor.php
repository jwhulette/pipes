<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
use SplFileObject;

class CsvExtractor implements ExtractorInterface
{
    protected Frame $frame;

    protected string $file;

    protected string $delimiter = ',';

    protected string $enclosure = '\'';

    protected int $skipLines = 0;

    protected bool $hasHeader = true;

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
            /** @var array<int,string>|false $header */
            $header = $file->fgetcsv($this->delimiter, $this->enclosure);

            if ($header !== \false && ! empty($header)) {
                $this->frame->setHeader($header);

                // Go back to the beginning of the file
                $file->rewind();
            }
        }

        // Skip the number of lines minus one as it's a zero based index
        if ($this->skipLines > 0) {
            $file->seek($this->skipLines - 1);
        }

        while (! $file->eof()) {
            $line = $file->fgetcsv($this->delimiter, $this->enclosure);

            if (! is_null($line) && $line !== \false) {
                yield $this->frame->setData($line);
            }
        }

        $this->frame->setEnd();

        $file = null;
    }
}
