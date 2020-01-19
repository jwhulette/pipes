<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Extractors;

use Generator;
use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Extractors\FixedWithExtractor;

class FixedWithExtractorTest extends TestCase
{
    public function testExtractorfixedWidthInstance()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract, [10]);

        $this->assertInstanceOf(FixedWithExtractor::class, $fixedWidth);
    }

    public function testHasGenerator()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract, [10]);

        $frame = $fixedWidth->extract();

        $this->assertInstanceOf(Generator::class, $frame);
    }

    public function testHasCollection()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract, [10]);

        $frameData = $fixedWidth->extract();

        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

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
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract);

        $frameData = $fixedWidth->setAllColumns(10)->extract();

        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/1969',
            'AMOUNT' => '$22.00',
        ];
        $this->assertEquals($expected, $frame->data->toArray());
    }

    public function testHasNoHeaderDifferentColumns()
    {
        $fixedWidth = new FixedWithExtractor($this->fixedWidthExtract, [1 => 10, 2 => 10, 3 => 10, 4 => 10]);

        $frameData = $fixedWidth->extract();

        $frame = $frameData->current();
        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/1969',
            'AMOUNT' => '$22.00',
        ];
        $this->assertEquals($expected, $frame->data->toArray());
    }
}
