<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Transformers;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\DateTimeTransformer;
use Tests\TestCase;

class DateTimeTransformerTest extends TestCase
{
    protected Frame $frame;

    protected function setUp(): void
    {
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
            '01/11/2000',
        ]);
    }

    /** @test */
    public function it_can_guess_a_date_string(): void
    {
        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB')
            ->transformColumn('DOB2');

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('1969-02-11', $result->data->get('DOB'));

        $this->assertEquals('2000-01-11', $result->data->get('DOB2'));
    }

    /** @test */
    public function it_can_guess_a_date_string_by_index(): void
    {
        $frame = new Frame;

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

        $this->assertEquals('1969-02-11', $result->data->slice(2, 1)->first());

        $this->assertEquals('2000-01-11', $result->data->slice(3, 1)->first());
    }

    /** @test */
    public function it_can_format_a_date_with_a_provided_format(): void
    {
        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB', 'Y-m-d', 'm/d/Y')
            ->transformColumn('DOB2', null, 'm/d/Y');

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals('1969-02-11', $result->data->get('DOB'));

        $this->assertEquals('2000-01-11', $result->data->get('DOB2'));
    }

    /** @test */
    public function it_can_format_a_date_with_a_provided_format_by_index(): void
    {
        $frame = new Frame;

        $frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
            '01/11/2000',
        ]);

        $transformer = (new DateTimeTransformer())
            ->transformColumn(2, 'Y-m-d', 'm/d/Y')
            ->transformColumn(3, null, 'm/d/Y');

        $result = $transformer->__invoke($frame);

        $this->assertEquals('1969-02-11', $result->data->slice(2, 1)->first());

        $this->assertEquals('2000-01-11', $result->data->slice(3, 1)->first());
    }

    /**
     * @test
     * @dataProvider dateTimeProvider
     */
    public function it_can_format_a_range_of_dates(string $date, string $expected): void
    {
        $frame = $this->frame->data->map(function ($item, $key) use ($date) {
            if ($key === 'DOB') {
                $item = $date;
            }

            return $item;
        });

        $this->frame->setData($frame->toArray());

        $transformer = (new DateTimeTransformer())
            ->transformColumn('DOB');

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals($expected, $result->data->get('DOB'));
    }

    /**
     * Data provider for testDateFormats.
     */
    public static function dateTimeProvider()
    {
        return [
            ['02/11/1969', '1969-02-11'],
            ['feb 11th 1969', '1969-02-11'],
            ['Feb 11th 1969', '1969-02-11'],
            ['11-FEB-1969', '1969-02-11'],
            ['1997-07-16T19:20+01:00', '1997-07-16'],
            ['1997-07-16T19:20:30+01:00', '1997-07-16'],
            ['1997-07-16T19:20:30.45+01:00', '1997-07-16'],
            ['1994-11-05T08:15:30-05:00', '1994-11-05'],
            ['1994-11-05T13:15:30Z', '1994-11-05'],
            ['Sun, 09 Mar 2008 16:05:07 GMT', '2008-03-09'],
            ['Sunday, March 09, 2008 4:05:07 PM', '2008-03-09'],
        ];
    }
}
