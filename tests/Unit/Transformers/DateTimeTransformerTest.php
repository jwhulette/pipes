<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Transformers\DateTimeTransformer;

class DateTimeTranformer extends TestCase
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

        $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
        ]);
    }

    public function testDateGuess()
    {
        $transformer = new DateTimeTransformer(['DOB']);

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals('1969-02-11', $result->data->last());
    }

    public function testDateInputFormat()
    {
        $transformer = new DateTimeTransformer(['DOB'], 'Y-m-d', 'm/d/Y');

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertEquals('1969-02-11', $result->data->last());
    }
}
