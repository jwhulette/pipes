<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\ConditionalTransformer;
use Orchestra\Testbench\TestCase;
use Override;
use PHPUnit\Framework\Attributes\Test;

class ConditionalTransformerTest extends TestCase
{
    protected Frame $frame;

    #[Test]
    public function it_can_transform_a_conditional(): void
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

        $this->assertEquals('Smithers', $result->getData()['LASTNAME']);
    }

    #[Test]
    public function it_can_transform_multiple_match_conditional(): void
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

        $this->assertEquals('Smithers', $result->getData()['LASTNAME']);

        $this->assertEquals('10/13/71', $result->getData()['DOB']);
    }

    #[Test]
    public function it_can_transform_multiple_match_and_multiple_replacements_conditional(): void
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

        $this->assertEquals('Smitty', $result->getData()['LASTNAME']);

        $this->assertEquals('10/13/74', $result->getData()['DOB']);
    }

    #[Override]
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
}
