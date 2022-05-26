<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Extractors\SqlExtractor;
use Jwhulette\Pipes\Frame;
use Tests\database\factories\SalesDataDatabaseFactory;
use Tests\TestCase;

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
    public function it_can_extract_from_database(): void
    {
        $builder = DB::table($this->table)->select('country, order_date');
        $sql = (new SqlExtractor())->setQueryBuilder($builder);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }
}
