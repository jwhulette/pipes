<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Transformers;

use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Transformers\TrimTransformer;
use Tests\TestCase;

class TrimTransformerTest extends TestCase
{
    protected Frame $frame;

    protected function setUp(): void
    {
        $this->frame = new Frame();

        $this->frame->setHeader([
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
        ]);
    }

    public function testTrimAllColumns()
    {
        $transformer = (new TrimTransformer())->transformAllColumns();

        $this->frame->setData([
            '  BOB   ',
            '  SMITH   ',
            '  02/11/1969   ',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testThrowsExeception()
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

    public function testLtrimAllColumns()
    {
        $transformer = (new TrimTransformer())->transformAllColumns('ltrim');

        $this->frame->setData([
            '  BOB',
            '  SMITH',
            '  02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimAllColumns()
    {
        $transformer = (new TrimTransformer())->transformAllColumns('rtrim');

        $this->frame->setData([
            'BOB   ',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimColumns()
    {
        $transformer = (new TrimTransformer())->transformColumn('LASTNAME');

        $this->frame->setData([
            'BOB  ',
            '  SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB  ', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimColumnsByIndex()
    {
        $transformer = (new TrimTransformer())->transformColumn(1);

        $frame = new Frame();

        $frame->setData([
            'BOB  ',
            '  SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($frame);

        $this->assertEquals(['BOB  ', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimColumns()
    {
        $transformer = (new TrimTransformer())->transformColumn('LASTNAME', 'ltrim');

        $this->frame->setData([
            'BOB',
            '  SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimColumnsByIndex()
    {
        $transformer = (new TrimTransformer())->transformColumn(1, 'ltrim');

        $frame = new Frame();

        $frame->setData([
            'BOB',
            '  SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimColumns()
    {
        $transformer = (new TrimTransformer())->transformAllColumns('rtrim');

        $this->frame->setData([
            'BOB ',
            'SMITH   ',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimAllColumnsWithMask()
    {
        $transformer = (new TrimTransformer())->transformAllColumns('trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            '$SMITH',
            '02/11/1969$',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testTrimColumnsWithMask()
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'trim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH',
            '02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB', 'SMITH', '02/11/1969'], $result->data->values()->toArray());
    }

    public function testLtrimColumnsWithMask()
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'ltrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            '$SMITH',
            '$02/11/1969',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['BOB$', '$SMITH', '$02/11/1969'], $result->data->values()->toArray());
    }

    public function testRtrimColumnsWithMask()
    {
        $transformer = (new TrimTransformer())->transformColumn('FIRSTNAME', 'rtrim', '$');

        $this->frame->setData([
            '$$$BOB$',
            'SMITH$',
            '02/11/1969$',
        ]);

        $result = $transformer->__invoke($this->frame);

        $this->assertEquals(['$$$BOB', 'SMITH$', '02/11/1969$'], $result->data->values()->toArray());
    }
}
