<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Jwhulette\Pipes\Frame;
use Tests\TestCase;

class FrameTest extends TestCase
{
    protected Frame $frame;

    protected array $testHeader;

    protected array $testData;

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

        $this->assertTrue($this->frame->getEnd());
    }

    public function testFrameHeader()
    {
        $this->frame->setHeader($this->testHeader);

        $this->assertSame($this->testHeader, $this->frame->getHeader()->toArray());
    }

    public function testFrameData()
    {
        $this->frame->setData($this->testData);

        $this->assertSame($this->testData, $this->frame->getData()->toArray());
    }

    public function testFrameHeaderData()
    {
        $this->frame->setHeader($this->testHeader);

        $this->frame->setData($this->testData);

        $this->assertSame($this->testHeaderData, $this->frame->getData()->toArray());
    }

    public function testFrameAttribute()
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->assertSame('no', $this->frame->getAttribute('valid'));
    }

    public function testFrameAttributes()
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->frame->setAttribute(['test'=>'yes']);

        $this->assertSame('no', $this->frame->getAttribute('valid'));

        $this->assertSame('yes', $this->frame->getAttribute('test'));
    }
}
