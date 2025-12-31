<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\CaseTransformer;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaseTransformerTest extends TestCase
{
    protected Frame $frame;

    #[Test]
    public function it_can_convert_a_value_to_lower_case(): void
    {
        $transformer = (new CaseTransformer())
            ->transformColumn('LASTNAME', 'lower')
            ->transformColumn('FIRSTNAME', 'lower');

        $result = $transformer->__invoke($this->frame);

        $expected = [
            'FIRSTNAME' => 'bob',
            'LASTNAME'  => 'smith',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->getData()->all());
    }

    #[Test]
    public function it_can_convert_a_value_to_lower_case_by_index(): void
    {
        $transformer = (new CaseTransformer())
            ->transformColumn(0, 'lower')
            ->transformColumn(1, 'lower');

        $this->frame = new Frame();

        $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $expected = [
            'bob',
            'smith',
            '02/11/1969',
        ];

        $this->assertEquals($expected, $result->getData()->all());
    }

    #[Test]
    public function it_can_convert_a_value_to_upper_case(): void
    {
        $transformer = (new CaseTransformer())
            ->transformColumn('LASTNAME', 'Upper');

        $result = $transformer->__invoke($this->frame);

        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'SMITH',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->getData()->all());
    }

    #[Test]
    public function it_can_convert_a_value_to_title_case(): void
    {
        $transformer = (new CaseTransformer())
            ->transformColumn('LASTNAME', 'TITLE');

        $result = $transformer->__invoke($this->frame);

        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME'  => 'Smith',
            'DOB'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $result->getData()->all());
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
