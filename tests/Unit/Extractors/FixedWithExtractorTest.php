<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use Tests\TestCase;
use jwhulette\pipes\Extractors\FixedWithExtractor;

class FixedWithExtractorTest extends TestCase
{
    public function testHasHeader()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract);
        $frameData = $fixedWidth->setAllColumns(10)->extract();
        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];

        $this->assertEquals($expected, $frame->data->keys()->toArray());
    }

    public function testHasNoHeaderAllColumns()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtractNoHeader);
        $frameData = $fixedWidth->setNoHeader()->setAllColumns(10)->extract();
        $frame = $frameData->current();
        $expected = [
            'BOB',
            'SMITH',
            '02/11/1969',
            '$22.00',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }

    public function testHasNoHeaderDifferentColumns()
    {
        $widths = [1 => 10, 2 => 10, 3 => 10, 4 => 10];
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtractNoHeader, $widths);
        $frameData = $fixedWidth->setNoHeader()->extract();
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
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract);
        $fixedWidth->setskipLines(3);
        $frameData = $fixedWidth->setAllColumns(10)->extract();
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
