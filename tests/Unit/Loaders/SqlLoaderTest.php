<?php

namespace Jwhulette\Pipes\Tests\Unit\Loaders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Loaders\SqlLoader;
use Tests\TestCase;

class SqlLoaderTest extends TestCase
{
    use RefreshDatabase;

    protected Frame $frame;

    protected Collection $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frame = new Frame();

        $this->frame->setHeader([
            'first_name',
            'last_name',
            'dob',
        ]);
    }

    public function testSqlLoaderInstance()
    {
        $loader = new SqlLoader('test');

        $data = $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
        ]);

        $loader->load($data);

        $this->assertInstanceOf(SqlLoader::class, $loader);
    }

    public function testWillLoadByBatch()
    {
        $loader = new SqlLoader('test');

        $loader->setBatchSize(3);

        for ($x = 0; $x < 5; $x++) {
            $data = $this->frame->setData([
                'BOB',
                'SMITH',
                '02/11/1969',
            ]);

            if ($x === 4) {
                $this->frame->setEnd();
            }

            $loader->load($data);
        }

        $count = DB::table('test')->count();

        $this->assertEquals(5, $count);
    }

    public function testUseCustomColumnNames()
    {
        $columns = ['first_name', 'last_name', 'dob'];

        $loader = new SqlLoader('test');

        $loader->setBatchSize(1)
            ->setSqlColumnNames($columns);

        $data = $this->frame->setData([
            'BOBBO',
            'SMITH',
            '02/11/1969',
        ]);

        $loader->load($data);

        $count = DB::table('test')->count();

        $this->assertEquals(1, $count);
    }
}
