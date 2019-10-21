<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use SplFileObject;
use jwhulette\pipes\Frame;

class CsvExtractor implements ExtractorInterface
{
    /** @var string */
    protected $delimiter;

    /** @var string */
    protected $enclosure;

    /** @var string */
    protected $file;

    /** @var int */
    protected $skipHeaderLines;

    /** @var \jwhulette\pipes\Frame */
    protected $frame;

    /**
     * @param string $file
     * @param int    $skipHeaderLines
     * @param string $delimiter
     * @param string $enclosure
     */
    public function __construct(string $file, int $skipHeaderLines = 1, string $delimiter = ',', string $enclosure = '\'')
    {
        $this->file = $file;

        $this->delimiter = $delimiter;

        $this->enclosure = $enclosure;

        $this->skipHeaderLines = $skipHeaderLines;

        $this->frame = new Frame();
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

        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

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
        
        $this->end();
        yield $this->frame;

        $file = null;
    }

    /**
     * Set the extractor end flag
     */
    public function end(): void
    {
        $this->frame->setEnd();
    }
}
