<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\Frame;

class FrameTest extends TestCase
{
    protected $frame;
    protected $testHeader;
    protected $testData;

    protected function setUp(): void
    {
        $this->frame = new Frame();
        $this->testHeader = ['first_name', 'last_name'];
        $this->testData = ['bob', 'smith'];
        $this->testHeaderData = [
            'first_name' => 'bob',
            'last_name'  => 'smith',
        ];
    }

    public function testFrameEnd()
    {
        $this->frame->setEnd();

        $this->assertTrue($this->frame->end);
    }

    public function testFrameHeader()
    {
        $this->frame->setHeader($this->testHeader);

        $this->assertEquals($this->testHeader, $this->frame->header->toArray());
    }

    public function testFrameData()
    {
        $this->frame->setData($this->testData);

        $this->assertEquals($this->testData, $this->frame->data->toArray());
    }

    public function testFrameHeaderData()
    {
        $this->frame->setHeader($this->testHeader);

        $this->frame->setData($this->testData);

        $this->assertEquals($this->testHeaderData, $this->frame->data->toArray());
    }

    public function testFrameAttribute()
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->assertEquals('no', $this->frame->attribute['valid']);
    }

    public function testFrameAttributes()
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->frame->setAttribute(['test'=>'yes']);

        $this->assertEquals('no', $this->frame->attribute['valid']);

        $this->assertEquals('yes', $this->frame->attribute['test']);
    }
}
