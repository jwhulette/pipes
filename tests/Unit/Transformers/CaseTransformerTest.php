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

    public function testConvertColumnsLower()
    {
        $transformer = new CaseTransformer();
        $transformer->transformColumn('LASTNAME', 'lower');
        $transformer->transformColumn('FIRSTNAME', 'lower');
        $result = $transformer->__invoke($this->frame);
        $expected = [
            'FIRSTNAME' => 'bob',
            'LASTNAME'  => 'smith',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->data->toArray());
    }

    public function testConvertColumnsUpper()
    {
        $transformer = new CaseTransformer();
        $transformer->transformColumn('LASTNAME', 'Upper');
        $result = $transformer->__invoke($this->frame);
        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'SMITH',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->data->toArray());
    }

    public function testConvertColumnsTitle()
    {
        $transformer = new CaseTransformer();
        $transformer->transformColumn('LASTNAME', 'TITLE');
        $result = $transformer->__invoke($this->frame);
        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'Smith',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->data->toArray());
    }
}
