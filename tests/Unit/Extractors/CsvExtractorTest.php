<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use jwhulette\pipes\Extractors\CsvExtractor;
use Tests\TestCase;

class CsvExtractorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function csv_file_has_header(): void
    {
        $csv = new CsvExtractor('tests/artifacts/test_file_with_header.csv');

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

    /** @test */
    public function csv_file_has_no_header(): void
    {
        $csv = new CsvExtractor('tests/artifacts/test_file_with_no_header.csv');

        $csv->setNoHeader();

        $frameData = $csv->extract();

        $frame = $frameData->current();

        $expected = [
            'BOB',
            'SMITHY',
            '12/31/69',
            '$22.22',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }

    /** @test */
    public function csv_can_skip_lines(): void
    {
        $csv = new CsvExtractor('tests/artifacts/test_file_with_header.csv');

        $csv->setSkipLines(2);

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
