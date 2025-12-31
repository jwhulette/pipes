<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\PhoneTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhoneTransformerTest extends TestCase
{
    /**
     * Data provider for testPhoneTransformation.
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

    #[Test]
    #[DataProvider('phoneProvider')]
    public function it_can_transform_a_phone_number(string $phone, string $expected): void
    {
        $frame = new Frame();

        $frame->setHeader(['PHONE']);

        $frame->setData([$phone]);

        $transformer = (new PhoneTransformer())->transformColumn('PHONE');

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->getData()->first());
    }

    #[Test]
    #[DataProvider('phoneProvider')]
    public function it_can_transform_a_phone_number_by_index($phone, $expected): void
    {
        $frame = new Frame();

        $frame->setData([$phone]);

        $transformer = (new PhoneTransformer())->transformColumn(0);

        $result = $transformer->__invoke($frame);

        $this->assertSame($expected, $result->getData()->first());
    }
}
