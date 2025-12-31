<?php

declare(strict_types=1);

namespace Tests\Unit\Transformers;

use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\TrimTransformer;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrimTransformerTest extends TestCase
{
    protected Frame $frame;

    #[Test]
    public function it_can_trim_all_columns(): void
    {
        $transformer = (new TrimTransformer())->transformAllColumns();

        $this->frame->setData([
            '  BOB   ',
            '  SMITH   ',
            '  02/11/1969   ',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_will_throw_exception_on_invalid_trim_type(): void
    {
        $this->expectException(PipesInvalidArgumentException::class);

        $transformer = (new TrimTransformer())->transformAllColumns('ltrims');

        $this->frame->setData([
            '  BOB',
            '  SMITH',
            '  02/11/1969',
        ]);

        $transformer->__invoke($this->frame);
    }

    #[Test]
    public function it_can_ltrim_all_columns(): void
    {
        $transformer = (new TrimTransformer())->transformAllColumns('ltrim');

        $this->frame->setData([
            '  BOB',
            '  SMITH',
            '  02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_rtrim_all_columns(): void
    {
        $transformer = (new TrimTransformer())->transformAllColumns('rtrim');

        $this->frame->setData([
            'BOB   ',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_trim_a_specific_column(): void
    {
        $transformer = (new TrimTransformer())->transformColumn('LASTNAME');

        $this->frame->setData([
            'BOB  ',
            '  SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB  ', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_trim_a_specific_column_by_index(): void
    {
        $transformer = (new TrimTransformer())->transformColumn(1);

        $frame = new Frame();

        $frame->setData([
            'BOB  ',
            '  SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($frame);

        $this->assertEquals(['BOB  ', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_ltrim_a_specific_column(): void
    {
        $transformer = (new TrimTransformer())->transformColumn('LASTNAME', 'ltrim');

        $this->frame->setData([
            'BOB',
            '  SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_ltrim_a_specific_column_by_index(): void
    {
        $transformer = (new TrimTransformer())->transformColumn(1, 'ltrim');

        $frame = new Frame();

        $frame->setData([
            'BOB',
            '  SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_rtrim_a_specific_column(): void
    {
        $transformer = (new TrimTransformer())->transformAllColumns('rtrim');

        $this->frame->setData([
            'BOB ',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_trim_all_columns_with_a_supplied_mask(): void
    {
        $transformer = (new TrimTransformer())->transformAllColumns('trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            '$SMITH',
            '02/11/1969$',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_trim_a_specific_columns_with_a_supplied_mask(): void
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_ltrim_a_specific_columns_with_a_supplied_mask(): void
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'ltrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            '$SMITH',
            '$02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB$', '$SMITH', '$02/11/1969'], $result->getData()->values()->all());
    }

    #[Test]
    public function it_can_rtrim_a_specific_columns_with_a_supplied_mask(): void
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'rtrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH$',
            '02/11/1969$',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['$$$BOB', 'SMITH$', '02/11/1969$'], $result->getData()->values()->all());
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
    }
}
