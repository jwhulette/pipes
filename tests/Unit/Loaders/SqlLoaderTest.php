<?php

declare(strict_types=1);

namespace Tests\Unit\Loaders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Loaders\SqlLoader;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SqlLoaderTest extends TestCase
{
    protected Frame $frame;

    protected Collection $data;

    #[Test]
    public function it_will_return_sql_loader_interface(): void
    {
        $loader = new SqlLoader('test');

        $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
        ]);

        $loader->load($this->frame);

        $this->assertInstanceOf(SqlLoader::class, $loader);
    }

    #[Test]
    public function it_will_load_records_in_a_batch(): void
    {
        $loader = new SqlLoader('test');

        $loader->setBatchSize(3);

        for ($x = 0; $x < 5; $x++) {
            $this->frame->setData([
                'BOB',
                'SMITH',
                '02/11/1969',
            ]);

            if ($x === 4) {
                $this->frame->setEnd();
            }

            $loader->load($this->frame);
        }

        $count = DB::table('test')->count();

        $this->assertEquals(5, $count);
    }

    #[Test]
    public function it_will_insert_data_with_custom_column_names(): void
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

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(getcwd() . '/tests/migrations');

        $this->frame = new Frame();

        $this->frame->setHeader([
            'first_name',
            'last_name',
            'dob',
        ]);
    }
}
