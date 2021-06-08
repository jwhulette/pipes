<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\ZipcodeTransformer;

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
     * Data providor for zipcodes.
     */
    public static function zipcodeProvider()
    {
        return [
            ['12345', '12345'],
            ['12345+678', '12345'],
            ['123', '123'],
        ];
    }

    /**
     * @param string $zip
     * @param string $expected
     *
     * @dataProvider zipcodeProvider
     */
    public function testZipcodeTransfromation($zip, $expected)
    {
        $this->frame->setData([$zip]);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame($expected, $result->data->first());
    }

    /**
     * @param string $zip
     * @param string $expected
     *
     * @dataProvider zipcodeProvider
     */
    public function testZipcodeTransfromationByIndex($zip, $expected)
    {
        $frame = new Frame;

        $frame->setData([$zip]);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0);

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->data->first());
    }

    public function testZipcodeTransfromationWithFillLimit5()
    {
        $this->frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padleft');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('00000', $result->data->first());
    }

    public function testZipcodeTransfromationByIndexWithFillLimit5()
    {
        $frame = new Frame;

        $frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padleft');

        $result = $transformer->__invoke($frame);

        $this->assertSame('00000', $result->data->first());
    }

    public function testZipcodeTransfromationWithFillLimitOther()
    {
        $this->frame->setData(['']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padleft', 10);

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('0000000000', $result->data->first());
    }

    public function testZipcodeTransfromationByIndexWithFillLimitOther()
    {
        $frame = new Frame;
        $frame->setData(['']);
        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padleft', 10);
        $result = $transformer->__invoke($frame);

        $this->assertSame('0000000000', $result->data->first());
    }

    public function testZipcodeTransfromationWithPadLeft()
    {
        $this->frame->setData(['122']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padleft');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('00122', $result->data->first());
    }

    public function testZipcodeTransfromationByIndexWithPadLeft()
    {
        $frame = new Frame;

        $frame->setData(['122']);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padleft');

        $result = $transformer->__invoke($frame);

        $this->assertSame('00122', $result->data->first());
    }

    public function testZipcodeTransfromationWithPadRight()
    {
        $this->frame->setData(['122']);

        $transformer = (new ZipcodeTransformer())->tranformColumn('zip', 'padright');

        $result = $transformer->__invoke($this->frame);

        $this->assertSame('12200', $result->data->first());
    }

    public function testZipcodeTransfromationByIndexWithPadRight()
    {
        $frame = new Frame;

        $frame->setData(['122']);

        $transformer = (new ZipcodeTransformer())->tranformColumn(0, 'padright');

        $result = $transformer->__invoke($frame);

        $this->assertSame('12200', $result->data->first());
    }
}
