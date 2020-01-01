<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use SplFileObject;
use jwhulette\pipes\Frame;

class CsvLoader implements LoaderInterface
{
    /** @var string */
    protected $delimiter;

    /** @var string */
    protected $enclosure;

    /** @var SplFileObject */
    protected $file;

    /** @var string */
    protected $escapeChar;

    /**
     * CsvLoader.
     *
     * @param string $ouputfile
     * @param string $delimiter
     * @param string $enclosure
     */
    public function __construct(string $ouputfile, string $delimiter = ',', string $enclosure = '"', $escapeChar = '\\')
    {
        $this->file = new SplFileObject($ouputfile, 'w');
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
    }

    /**
     * Write the data to the file.
     *
     * @param \jwhulette\pipes\Frame $frame
     */
    public function load(Frame $frame): void
    {
        $this->file->fputcsv(
            $frame->data->values()->toArray(),
            $this->delimiter,
            $this->enclosure,
            $this->escapeChar
        );
    }
}
