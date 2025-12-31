<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\DateTimeTransformer;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DateTimeTransformerTest extends TestCase
{
    protected Frame $frame;

    /**
     * Data provider for testDateFormats.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    public static function dateTimeProvider(): array
    {
        return [
            ['02/11/1969', '1969-02-11 00:00:00'],
            ['feb 11th 1969', '1969-02-11 00:00:00'],
            ['Feb 11th 1969', '1969-02-11 00:00:00'],
            ['11-FEB-1969', '1969-02-11 00:00:00'],
            ['1997-07-16T19:20+01:00', '1997-07-16 19:20:00'],
            ['1997-07-16T19:20:30+01:00', '1997-07-16 19:20:30'],
            ['1997-07-16T19:20:30.45+01:00', '1997-07-16 19:20:30'],
            ['1994-11-05T08:15:30-05:00', '1994-11-05 08:15:30'],
            ['1994-11-05T13:15:30Z', '1994-11-05 13:15:30'],
            ['Sun, 09 Mar 2008 16:05:07 GMT', '2008-03-09 16:05:07'],
            ['Sunday, March 09, 2008 4:05:07 PM', '2008-03-09 16:05:07'],
        ];
    }

    #[Test]
    public function it_can_guess_a_date_string(): void
    {
        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB')
            ->transformColumn('DOB2');

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('1969-02-11 00:00:00', $result->getData()->get('DOB'));

        $this->assertEquals('2000-01-11 19:20:00', $result->getData()->get('DOB2'));
    }

    #[Test]
    public function it_can_guess_a_date_by_index(): void
    {
        $frame = new Frame();

        $frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
            '01/11/2000',
        ]);

        $transformer = (new DateTimeTransformer())
            ->transformColumn(2)
            ->transformColumn(3);

        $result = $transformer->__invoke($frame);

        $this->assertEquals('1969-02-11 00:00:00', $result->getData()->slice(2, 1)->first());

        $this->assertEquals('2000-01-11 00:00:00', $result->getData()->slice(3, 1)->first());
    }

    #[Test]
    public function it_can_format_a_date_with_a_provided_format(): void
    {
        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB', 'Y-m-d', 'm/d/Y')
            ->transformColumn('DOB2', null, 'm/d/Y H:i:s');

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('1969-02-11', $result->getData()->get('DOB'));

        $this->assertEquals('2000-01-11 19:20:00', $result->getData()->get('DOB2'));
    }

    #[Test]
    public function it_can_format_a_date_with_a_provided_format_by_index(): void
    {
        $frame = new Frame();

        $frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
            '01/11/2000 00:00:00',
        ]);

        $transformer = (new DateTimeTransformer())
            ->transformColumn(2, 'Y-m-d', 'm/d/Y')
            ->transformColumn(3, null, 'm/d/Y h:i:s');

        $result = $transformer->__invoke($frame);

        $this->assertEquals('1969-02-11', $result->getData()->slice(2, 1)->first());

        $this->assertEquals('2000-01-11 00:00:00', $result->getData()->slice(3, 1)->first());
    }

    #[Test]
    #[DataProvider('dateTimeProvider')]
    public function it_can_format_a_range_of_dates(string $date, string $expected): void
    {
        $frame = new Frame();

        $frame->setHeader([
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
        ]);

        $frame->setData([
            'BOB',
            'SMITH',
            $date,
        ]);

        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB');

        $result = $transformer->__invoke($frame);

        $this->assertSame(
            $expected,
            $result->getData()->get('DOB')
        );
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->frame = new Frame();

        $this->frame->setHeader([
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'DOB2',
        ]);

        $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
            '01/11/2000 19:20:00',
        ]);
    }
}
