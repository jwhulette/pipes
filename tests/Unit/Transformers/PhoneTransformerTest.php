<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Transformers\PhoneTransformer;

class PhoneTransformerTest extends TestCase
{
    /**
     * @param string $phone
     * @param string $expected
     *
     * @dataProvider phoneProvider
     */
    public function testPhoneTransfromation($phone, $expected)
    {
        $frame = new Frame();

        $frame->setHeader(['PHONE']);

        $frame->setData([$phone]);

        $transformer = new PhoneTransformer(['PHONE']);

        $result = $transformer->__invoke($frame);

        $this->assertInstanceOf(Frame::class, $result);

        $this->assertSame($expected, $result->data->first());
    }

    /**
     * Data providor for testPhoneTransfromation.
     */
    public static function phoneProvider()
    {
        return [
            ['555-555-5555', '5555555555'],
            ['555 555 5555', '5555555555'],
            ['(555) 555-5555', '5555555555'],
            ['(555) 555 5555', '5555555555'],
            ['555.555.5555', '5555555555'],
            ['555.555.5555 ext 555', '5555555555'],
        ];
    }
}
