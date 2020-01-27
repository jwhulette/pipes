<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use Tests\TestCase;
use jwhulette\pipes\Extractors\CsvExtractor;

class CsvExtractorTest extends TestCase
{
    public function testHasHeader()
    {
        $csv = new CsvExtractor($this->csvExtract);
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
        $csv = new CsvExtractor($this->csvExtractNoHeader);
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
    }

    public function testSkipLines()
    {
        $csv = new CsvExtractor($this->csvExtract);
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
