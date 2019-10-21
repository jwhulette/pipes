<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Transformers\CaseTransformer;

class CaseTransformerTest extends TestCase
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

    public function testConvertColumns()
    {
        $transformer = new CaseTransformer(['LASTNAME'], 'lower');

        $result = $transformer->__invoke($this->frame);

        $this->assertInstanceOf(Frame::class, $result);

        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'smith',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->data->toArray());
    }
}
