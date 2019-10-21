<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Transformers\TrimTransformer;

class TrimTransformerTest extends TestCase
{
    /** @var Frame */
    protected $frame;

    protected function setUp(): void
    {
        $this->frame = new Frame();

        $this->frame->setHeader([
                'FIRSTNAME',
                'LASTNAME',
                'DOB',
            ]);
    }

    public function testTrimAllColumns()
    {
        $transformer = new TrimTransformer();

        $this->frame->setData([
            '  BOB   ',
            '  SMITH   ',
            '  02/11/1969   ',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimAllColumns()
    {
        $transformer = new TrimTransformer([], 'ltrim');

        $this->frame->setData([
            '  BOB',
            '  SMITH',
            '  02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimAllColumns()
    {
        $transformer = new TrimTransformer([], 'rtrim');

        $this->frame->setData([
            'BOB   ',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimColumns()
    {
        $transformer = new TrimTransformer(['LASTNAME'], 'trim');

        $this->frame->setData([
            'BOB',
            '  SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimColumns()
    {
        $transformer = new TrimTransformer(['LASTNAME'], 'ltrim');

        $this->frame->setData([
            'BOB',
            '  SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimColumns()
    {
        $transformer = new TrimTransformer([], 'rtrim');

        $this->frame->setData([
            'BOB',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimAllColumnsWithMask()
    {
        $transformer = new TrimTransformer([], 'trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimColumnsWithMask()
    {
        $transformer = new TrimTransformer(['FIRSTNAME'], 'trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimColumnsWithMask()
    {
        $transformer = new TrimTransformer(['FIRSTNAME'], 'ltrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['BOB$', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimColumnsWithMask()
    {
        $transformer = new TrimTransformer(['FIRSTNAME'], 'rtrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals(['$$$BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }
}
