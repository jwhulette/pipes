<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use Tests\TestCase;
use org\bovigo\vfs\vfsStream;
use Illuminate\Support\Facades\File;
use Tests\factories\DataFileFactory;
use org\bovigo\vfs\vfsStreamDirectory;
use jwhulette\pipes\Extractors\CsvExtractor;

class CsvExtractorTest extends TestCase
{
    protected string $extract;

    protected string $extractNoHeader;

    protected vfsStreamDirectory $vfs;

    public function setUp(): void
    {
        parent::setUp();

        $directory = [
            'csv_extractor.csv',
            'csv_no_header_extractor.csv',
        ];

        $this->vfs = vfsStream::setup(sys_get_temp_dir(), null, $directory);

        $header = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];
        $this->extract = $this->vfs->url() . '/csv_extractor.csv';

        $this->extractNoHeader = $this->vfs->url() . '/csv_no_header_extractor.csv';

        (new DataFileFactory($this->extract))
            ->asText()
            ->setHeader($header)
            ->create();

        (new DataFileFactory($this->extractNoHeader))
            ->asText()
            ->create();
    }

    public function testHasHeader()
    {
        $csv = new CsvExtractor($this->extract);

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];

        $this->assertEquals($expected, $frame->header->values()->toArray());
    }

    public function testHasNoHeader()
    {
        $csv = new CsvExtractor($this->extractNoHeader);

        $csv->setNoHeader();

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'BOB',
            'SMITH',
            '02/11/1969',
            '$22.00',
        ];

        $this->assertEquals($expected, $frame->data->toArray());

        File::delete($this->extract);
    }

    public function testSkipLines()
    {
        $csv = new CsvExtractor($this->extract);

        $csv->setskipLines(3);

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME' => 'LISA',
            'LASTNAME'  => 'SMITH',
            'DOB'       => '02/11/2001',
            'AMOUNT'    => '$50.00',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
