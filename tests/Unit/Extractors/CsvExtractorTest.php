<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use Generator;
use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Extractors\CsvExtractor;

class CsvExtractorTest extends TestCase
{
    public function testExtractorCsvInstance()
    {
        $csv = new CsvExtractor($this->csvExtract);

        $this->assertInstanceOf(CsvExtractor::class, $csv);
    }

    public function testHasGenerator()
    {
        $csv = new CsvExtractor($this->csvExtract);

        $frame = $csv->extract();

        $this->assertInstanceOf(Generator::class, $frame);
    }

    public function testHasCollection()
    {
        $csv = new CsvExtractor($this->csvExtract);

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    public function testHasHeader()
    {
        $csv = new CsvExtractor($this->csvExtract, 0);

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
        $csv = new CsvExtractor($this->csvExtract);

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'SMITH',
            'DOB'       => '02/11/1969',
            'AMOUNT'    => '$22.00',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
