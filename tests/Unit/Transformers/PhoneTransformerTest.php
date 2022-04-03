<?php

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\PhoneTransformer;
use Tests\TestCase;

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

        $transformer = (new PhoneTransformer())->transformColumn('PHONE');

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->getData()->first());
    }

    /**
     * @param string $phone
     * @param string $expected
     *
     * @dataProvider phoneProvider
     */
    public function testPhoneTransfromationByIndex($phone, $expected)
    {
        $frame = new Frame();

        $frame->setData([$phone]);

        $transformer = (new PhoneTransformer())->transformColumn(0);

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->getData()->first());
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
