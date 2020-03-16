<?php

namespace Tests\factories;

use SplFileObject;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class DataFileFactory
{
    protected string $delimiter;
    protected string $enclosure;
    protected string $escapeCharacter;
    protected int $width;
    protected ?array $header = null;
    protected string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Create a file.
     */
    public function create(): void
    {
        switch ($this->fileType) {
            case 'txt':
                $this->createTextFile();
                break;
            case 'fw':
                $this->createFixedWidthFile();
                break;
            case 'xlsx':
                $this->createXlsxFile();
                break;
            default:
                // code...
                break;
        }
    }

    /**
     * Set the file as an xlsx.
     *
     * @return DataFileFactory
     */
    public function asXlsx(): DataFileFactory
    {
        $this->fileType = 'xlsx';

        return $this;
    }

    /**
     * Set the file as a fixed with file.
     *
     * @param int $width
     *
     * @return DataFileFactory
     */
    public function asFixedWidth(int $width): DataFileFactory
    {
        $this->fileType = 'fw';
        $this->width = $width;

        return $this;
    }

    /**
     * Set the file as a text file.
     *
     * @param string $delimeter
     * @param string $enclosure
     * @param string $escapeCharacter
     *
     * @return DataFileFactory
     */
    public function asText(string $delimeter = ',', string $enclosure = '"', string $escapeCharacter = '\\'): DataFileFactory
    {
        $this->fileType = 'txt';
        $this->delimiter = $delimeter;
        $this->enclosure = $enclosure;
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    /**
     * Set a file header.
     *
     * @param array $header
     *
     * @return DataFileFactory
     */
    public function setHeader(array $header): DataFileFactory
    {
        $this->header = $header;

        return $this;
    }

    protected function createXlsxFile():void
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($this->file);

        if (! is_null($this->header)) {
            $rowFromValues = WriterEntityFactory::createRowFromArray($this->header);

            $writer->addRow($rowFromValues);
        }

        foreach ($this->data() as $items) {
            $rowFromValues = WriterEntityFactory::createRowFromArray($items);

            $writer->addRow($rowFromValues);
        }

        $writer->close();
    }

    /**
     * Create a fixed width file.
     */
    protected function createFixedWidthFile(): void
    {
        $file = new SplFileObject($this->file, 'w');
        if (! is_null($this->header)) {
            foreach ($this->header as $value) {
                $file->fwrite(\str_pad($value, $this->width));
            }
            $file->fwrite(PHP_EOL);
        }

        foreach ($this->data() as $line) {
            foreach ($line as $item) {
                $file->fwrite(\str_pad($item, $this->width));
            }
            $file->fwrite(PHP_EOL);
        }
    }

    /**
     * Create a text file.
     */
    protected function createTextFile(): void
    {
        $file = new SplFileObject($this->file, 'w');
        if (! is_null($this->header)) {
            $file->fputcsv(
                $this->header,
                $this->delimiter,
                $this->enclosure,
                $this->escapeCharacter
            );
        }

        foreach ($this->data() as $data) {
            $file->fputcsv(
                $data,
                $this->delimiter,
                $this->enclosure,
                $this->escapeCharacter
            );
        }
    }

    /**
     * Sample data.
     *
     * @return array
     */
    protected function data(): array
    {
        return [
            [
                'BOB',
                'SMITH',
                '02/11/1969',
                '$22.00',
            ],
            [
                'TOM',
                'SMITH',
                '02/11/1970',
                '$40.00',
            ],
            [
                'LISA',
                'SMITH',
                '02/11/2001',
                '$50.00',
            ],
        ];
    }
}
