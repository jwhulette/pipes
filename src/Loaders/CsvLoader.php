<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Loaders;

use DateInterval;
use DateTimeInterface;
use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Frame;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;

final class CsvLoader implements LoaderInterface
{
    private static Writer $instance;

    protected Options $options;

    protected string $file;

    public function __construct(string $ouputfile)
    {
        $this->options = new Options();

        $this->file = $ouputfile;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->options->FIELD_DELIMITER = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->options->FIELD_ENCLOSURE = $enclosure;

        return $this;
    }

    public function noBom(): self
    {
        $this->options->SHOULD_ADD_BOM = \false;

        return $this;
    }

    public function load(Frame $frame): void
    {
        $writer = self::getWriter($this->options);

        $writer->openToFile($this->file);

        /** @var array<int,bool|DateInterval|DateTimeInterface|float|int|string|null> $values */
        $values = $frame->data->values()->toArray();

        $writer->addRow(Row::fromValues($values));

        // Close the file
        if ($frame->end === true) {
            $writer->close();
        }
    }

    private static function getWriter(Options $options): Writer
    {
        // Check is $_instance has been set
        if (! isset(self::$instance)) {
            // Creates sets object to instance
            self::$instance = new Writer($options);
        }

        // Returns the instance
        return self::$instance;
    }
}
