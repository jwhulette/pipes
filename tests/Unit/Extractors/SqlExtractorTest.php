<?php

namespace Tests\Unit\Extractors;

use Tests\TestCase;
use Jwhulette\Pipes\Frame;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Extractors\SqlExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\database\factories\SalesDataDatabaseFactory;

class SqlExtractorTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'sales_data';

    protected function setUp(): void
    {
        parent::setUp();

        (new SalesDataDatabaseFactory($this->table))->create(10);
    }

    /** @test */
    public function test_query_builder()
    {
        $builder = DB::table($this->table)->select('country, order_date');
        $sql = (new SqlExtractor())->setQueryBuilder($builder);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }
}
