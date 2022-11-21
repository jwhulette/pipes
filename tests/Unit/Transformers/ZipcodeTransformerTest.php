<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\ZipcodeTransformer;
use Tests\TestCase;

class ZipcodeTransformerTest extends TestCase
{
    protected Frame $frame;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frame = new Frame();

        $this->frame->setHeader(['zip']);
    }

    /**
     * @test
     * @dataProvider zipcodeProvider
     */
    public function it_can_transform_postal_codes(string $zip, string $expected): void
    {
        $this->frame->setData([$zip]);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame($expected, $result->getData()->first());
    }

    /**
     * @test
     * @dataProvider zipcodeProvider
     */
    public function it_can_transform_postal_codes_by_index(string $zip, string $expected): void
    {
        $frame = new Frame();

        $frame->setData([$zip]);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0);

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->getData()->first());
    }

    /** @test */
    public function it_will_left_pad_a_zip_code_with_zeros(): void
    {
        $this->frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padleft');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('00000', $result->getData()->first());
    }

    /** @test */
    public function it_will_left_pad_a_zip_code_with_zeros_by_index(): void
    {
        $frame = new Frame();

        $frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padleft');

        $result = $transformer->__invoke($frame);

        $this->assertSame('00000', $result->getData()->first());
    }

    public function it_will_left_pad_a_zip_code_with_zeros_with_length(): void
    {
        $this->frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padleft', 10);

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('0000000000', $result->getData()->first());
    }

    public function it_will_left_pad_a_zip_code_with_zeros_with_length_by_index(): void
    {
        $frame = new Frame();
        $frame->setData(['']);
        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padleft', 10);
        $result = $transformer->__invoke($frame);

        $this->assertSame('0000000000', $result->getData()->first());
    }

    /**
     * Data provider.
     */
    public static function zipcodeProvider()
    {
        return [
            ['12345', '12345'],
            ['12345+678', '12345'],
            ['123', '123'],
        ];
    }
}
