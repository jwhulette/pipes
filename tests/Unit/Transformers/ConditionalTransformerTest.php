<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit\Transformers;

use jwhulette\pipes\Frame;
use Orchestra\Testbench\TestCase;
use jwhulette\pipes\Transformers\ConditionalTransformer;

class ConditionalTransformerTest extends TestCase
{
    protected Frame $frame;

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
        $match = [
                'FIRSTNAME' => 'BOB',
            ];

        $replace = [
            'LASTNAME' => 'Smithers',
        ];

        $transformer = (new ConditionalTransformer())
            ->addConditional($match, $replace);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('Smithers', $result->data['LASTNAME']);
    }

    public function testMultipleConditional()
    {
        $match = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
        ];

        $replace = [
            'LASTNAME' => 'Smithers',
            'DOB' => '10/13/71',
        ];

        $transformer = (new ConditionalTransformer())
            ->addConditional($match, $replace);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('Smithers', $result->data['LASTNAME']);

        $this->assertEquals('10/13/71', $result->data['DOB']);
    }

    public function testMultipleMultipleConditional()
    {
        $match1 = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
         ];

        $replace1 = [
            'LASTNAME' => 'Smithers',
            'DOB' => '10/13/71',
        ];

        $match2 = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'Smithers',
        ];

        $replace2 = [
            'LASTNAME' => 'Smitty',
            'DOB' => '10/13/74',
        ];

        $transformer = (new ConditionalTransformer())
            ->addConditional($match1, $replace1)
            ->addConditional($match2, $replace2);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('Smitty', $result->data['LASTNAME']);

        $this->assertEquals('10/13/74', $result->data['DOB']);
    }
}
