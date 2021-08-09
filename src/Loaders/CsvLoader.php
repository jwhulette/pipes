<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Loaders;

use SplFileObject;
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
     * @param Frame $frame
     */
    public function load(Frame $frame): void
    {
        $this->file->fputcsv(
            $frame->data->values()->toArray(),
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );

        // Close the file
        if ($frame->end === true) {
            unset($this->file);
        }
    }
}
