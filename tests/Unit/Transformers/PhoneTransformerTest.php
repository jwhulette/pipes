<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use jwhulette\pipes\Frame;
use jwhulette\pipes\Transformers\PhoneTransformer;
use Tests\TestCase;

class PhoneTransformerTest extends TestCase
{
    /**
     * @param string $phone
     * @param string $expected
     *
     * @dataProvider phoneProvider
     */
    public function testPhoneTransformation($phone, $expected): void
    {
        $frame = new Frame();

        $frame->setHeader(['PHONE']);

        $frame->setData([$phone]);

        $transformer = (new PhoneTransformer())->transformColumn('PHONE');

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->data->first());
    }

    /**
     * @param string $phone
     * @param string $expected
     *
     * @dataProvider phoneProvider
     */
    public function testPhoneTransformationByIndex($phone, $expected): void
    {
        $frame = new Frame();

        $frame->setData([$phone]);

        $transformer = (new PhoneTransformer())->transformColumn(0);

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->data->first());
    }

    /**
     * Data provider for testPhoneTransformation.
     */
    public static function phoneProvider()
    {
        return [
            // ['555-555-5555', '5555555555'],
            // ['555 555 5555', '5555555555'],
            // ['(555) 555-5555', '5555555555'],
            // ['(555) 555 5555', '5555555555'],
            // ['555.555.5555', '5555555555'],
            ['555.555.5555 ext 555', '5555555555'],
        ];
    }
}
