<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use jwhulette\pipes\Frame;
use Orchestra\Testbench\TestCase;
use jwhulette\pipes\Transformers\ConditionalTransformer;

class ConditionalTransformerTest extends TestCase
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

    public function testConditional()
    {
        $cond = [
            [
                'match' => [
                'FIRSTNAME' => 'BOB'
            ],
                'replace' => [
                    'LASTNAME' => 'Smithers'
                ]
            ]
        ];

        $transformer = new ConditionalTransformer($cond);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('Smithers', $result->data['LASTNAME']);
    }

    public function testMultipleConditional()
    {
        $cond = [
            [
                'match' => [
                'FIRSTNAME' => 'BOB',
                'LASTNAME' => 'SMITH'
            ],
                'replace' => [
                    'LASTNAME' => 'Smithers',
                    'DOB' => '10/13/71'
                ]
            ]
        ];

        $transformer = new ConditionalTransformer($cond);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('Smithers', $result->data['LASTNAME']);
        $this->assertEquals('10/13/71', $result->data['DOB']);
    }
}
