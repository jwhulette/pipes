<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Extractors;

use Illuminate\Support\Facades\File;
use Jwhulette\Pipes\Extractors\CsvExtractor;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tests\database\factories\DataFileFactory;
use Tests\TestCase;

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
        $this->extract = $this->vfs->url().'/csv_extractor.txt';

        $this->extractNoHeader = $this->vfs->url().'/csv_no_header_extractor.csv';

        (new DataFileFactory($this->extract))
            ->asText()
            ->setHeader($header)
            ->create();

        (new DataFileFactory($this->extractNoHeader))
            ->asText()
            ->create();
    }

    /** @test */
    public function it_has_header()
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

        $this->assertEquals($expected, $frame->getHeader()->values()->toArray());
    }

    /** @test */
    public function it_has_no_header()
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

        $this->assertEquals($expected, $frame->getData()->toArray());

        File::delete($this->extract);
    }

    /** @test */
    public function it_can_skip_lines()
    {
        $csv = new CsvExtractor($this->extract);

        $csv->setSkipLines(3);

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME' => 'LISA',
            'LASTNAME'  => 'SMITH',
            'DOB'       => '02/11/2001',
            'AMOUNT'    => '$50.00',
        ];

        $this->assertEquals($expected, $frame->getData()->toArray());
    }
}
