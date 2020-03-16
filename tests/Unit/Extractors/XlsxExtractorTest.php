<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Tests\factories\DataFileFactory;
use jwhulette\pipes\Extractors\XlsxExtractor;

class XlsxExtractorTest extends TestCase
{
    protected string $extract;
    protected string $extractNoHeader;

    public function setUp(): void
    {
        parent::setUp();

        $header = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];

        /*
         * Need to create a temp file as Spout Box is not able
         * to read from the vfStream file stream
         */
        File::makeDirectory($this->testFilesDirectory, 0777, true);

        $this->extract = $this->testFilesDirectory.'/extractor.xlsx';

        $this->extractNoHeader = $this->testFilesDirectory.'/no_header_extractor.xlsx';

        (new DataFileFactory($this->extract))
            ->asXlsx()
            ->setHeader($header)
            ->create();

        (new DataFileFactory($this->extractNoHeader))
            ->asXlsx()
            ->create();
    }

    public function tearDown(): void
    {
        File::cleanDirectory($this->testFilesDirectory);

        File::deleteDirectory($this->testFilesDirectory);
    }

    public function testFrameHasHeader()
    {
        $excel = new XlsxExtractor($this->extract);
        $frameData = $excel->extract();
        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];
        $expectedData = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/1969',
            'AMOUNT' => '$22.00',
        ];

        $this->assertEquals($expected, $frame->header->values()->toArray());

        $this->assertEquals($expectedData, $frame->data->toArray());
    }

    public function testHasNoHeader()
    {
        $excel = new XlsxExtractor($this->extractNoHeader);
        $frameData = $excel->setNoHeader()->extract();
        $frame = $frameData->current();
        $expected = [
            'BOB',
            'SMITH',
            '02/11/1969',
            '$22.00',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
