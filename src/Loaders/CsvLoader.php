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
    protected Options $options;

    protected string $file;

    private static Writer $instance;

    public function __construct(string $ouputfile)
    {
        $this->options = new Options();

        $this->file = $ouputfile;
    }

    /**
     * Set the file field delimiter.
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->options->FIELD_DELIMITER = $delimiter;

        return $this;
    }

    /**
     * Set the text field enclosure.
     */
    public function setEnclosure(string $enclosure): self
    {
        $this->options->FIELD_ENCLOSURE = $enclosure;

        return $this;
    }

    /**
     * Do not set a BOM on the file.
     *
     * @see https://en.wikipedia.org/wiki/Byte_order_mark
     */
    public function noBom(): self
    {
        $this->options->SHOULD_ADD_BOM = \false;

        return $this;
    }

    /**
     * Write a data frame to the file.
     */
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
