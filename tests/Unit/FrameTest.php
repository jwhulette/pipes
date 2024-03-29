<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Jwhulette\Pipes\Frame;
use Tests\TestCase;

class FrameTest extends TestCase
{
    protected $frame;

    protected $testHeader;

    protected $testData;

    protected array $testHeaderData;

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

    /** @test */
    public function it_sets_the_frame_end(): void
    {
        $this->frame->setEnd();

        $this->assertTrue($this->frame->getEnd());
    }

    /** @test */
    public function it_sets_the_frame_header(): void
    {
        $this->frame->setHeader($this->testHeader);

        $this->assertSame($this->testHeader, $this->frame->getHeader()->toArray());
    }

    /** @test */
    public function it_sets_the_frame_data(): void
    {
        $this->frame->setData($this->testData);

        $this->assertSame($this->testData, $this->frame->getData()->toArray());
    }

    /** @test */
    public function it_combines_the_frame_header_and_data(): void
    {
        $this->frame->setHeader($this->testHeader);

        $this->frame->setData($this->testData);

        $this->assertSame($this->testHeaderData, $this->frame->getData()->toArray());
    }

    /** @test */
    public function it_can_set_a_frame_attribute(): void
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->assertSame('no', $this->frame->getAttribute('valid'));
    }

    /** @test */
    public function it_can_set_multiple_frame_attributes(): void
    {
        $this->frame->setAttribute(['valid'=>'no']);

        $this->frame->setAttribute(['test'=>'yes']);

        $this->assertSame('no', $this->frame->getAttribute('valid'));

        $this->assertSame('yes', $this->frame->getAttribute('test'));
    }
}
