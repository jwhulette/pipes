<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Transformers;

use Jwhulette\Pipes\Extractors\XlsxExtractor;
use Tests\TestCase;

class XlsxExtractorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_skips_lines_from_xlsx_file(): void
    {
        $frameData = (new XlsxExtractor('tests/artifacts/test_file.xlsx'))
            ->setSheetIndex(3)
            ->setSkipLines(3)
            ->extract();

        $frame = $frameData->current();

        $expectedData = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '01/03/1970 19:00:25',
            'AMOUNT' => 24.22,
        ];

        $this->assertEquals($expectedData, $frame->data->toArray());
    }

    /** @test */
    public function it_formats_date_data_from_xlsx_file(): void
    {
        $frameData = (new XlsxExtractor('tests/artifacts/test_file.xlsx'))
            ->formatDates()
            ->extract();

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
            'DOB' => '12/31/1969',
            'AMOUNT' => 22.22,
        ];

        $this->assertEquals($expected, $frame->header->values()->toArray());

        $this->assertEquals($expectedData, $frame->data->toArray());
    }

    /** @test */
    public function it_extracts_data_from_xlsx_file_with_header(): void
    {
        $frameData = (new XlsxExtractor('tests/artifacts/test_file.xlsx'))
            ->extract();

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
            'DOB' => '1969-12-31 19:00:25',
            'AMOUNT' => 22.22,
        ];

        $this->assertEquals($expected, $frame->header->values()->toArray());

        $this->assertEquals($expectedData, $frame->data->toArray());
    }

    /** @test */
    public function it_extracts_data_from_xlsx_file_with_no_header(): void
    {
        $frameData = (new XlsxExtractor('tests/artifacts/test_file.xlsx'))
            ->setSheetIndex(2)
            ->setNoHeader()
            ->extract();

        $frame = $frameData->current();

        $expected = [
            'Pete',
            'Dragon',
            '1969-12-31 19:00:29',
            50.50,
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }

    /** @test */
    public function it_extracts_data_from_the_selected_sheet(): void
    {
        $frameData = (new XlsxExtractor('tests/artifacts/test_file.xlsx'))
            ->setSheetIndex(1)
            ->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME' => 'Tom',
            'LASTNAME' => 'Collins',
            'DOB' => '1980-04-11 00:00:00',
            'AMOUNT' => 50.50,
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
