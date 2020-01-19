<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Extractors\XlsxExtractor;

class XlsxExtractorTest extends TestCase
{
    public function testExtractorSpreadSheetInstance()
    {
        $xlsx = new XlsxExtractor($this->xlsxExtract);

        $this->assertInstanceOf(XlsxExtractor::class, $xlsx);
    }

    public function testHasCollection()
    {
        $csv = new XlsxExtractor($this->xlsxExtract);
        $frameData = $csv->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    public function testFrameHasHeader()
    {
        $excel = new XlsxExtractor($this->xlsxExtract);
        $frameData = $excel->extract();
        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'COST',
            'test2'
        ];

        $this->assertEquals($expected, $frame->header->values()->toArray());
    }

    public function testHasNoHeader()
    {
        $excel = new XlsxExtractor($this->xlsxExtract);
        $frameData = $excel->extract();
        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/69',
            'COST' => 22,
            'test2' => 'test'
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
