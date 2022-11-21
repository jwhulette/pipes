<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Extractors\SqlExtractor;
use Jwhulette\Pipes\Frame;
use Tests\factories\DatabaseFactory;
use Tests\TestCase;

class SqlExtractorTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'sales_data';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(getcwd() . '/tests/migrations');

        (new DatabaseFactory($this->table))->create(20);
    }

    /** @test */
    public function it_sets_the_database_table_name(): void
    {
        $sql = (new SqlExtractor())
            ->setTable($this->table);

        (new SalesDataDatabaseFactory($this->table))->create(10);
    }

    /** @test */
    public function it_can_run_a_select_query(): void
    {
        $builder = DB::table($this->table)->select('country, order_date');
        $sql = (new SqlExtractor())->setQueryBuilder($builder);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }
}
