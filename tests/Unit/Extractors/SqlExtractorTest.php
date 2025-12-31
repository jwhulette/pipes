<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Extractors\SqlExtractor;
use Jwhulette\Pipes\Frame;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\artifacts\SalesData;
use Tests\factories\DatabaseFactory;
use Tests\TestCase;

class SqlExtractorTest extends TestCase
{
    protected string $table = 'sales_data';

    #[Test]
    public function it_can_run_a_select_with_eloquent_builder(): void
    {
        $builder = SalesData::query()
            ->select(['country', 'order_date']);

        $sql = (new SqlExtractor())->setBuilder($builder);

        $frameData = $sql->extract();

        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    #[Test]
    public function it_can_run_a_select_with_query_builder(): void
    {
        $builder = DB::connection('testbench')
            ->table($this->table)
            ->select(['country', 'order_date']);

        $sql = (new SqlExtractor())->setBuilder($builder);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    #[Test]
    public function it_can_run_a_select_query_without_connection_set(): void
    {
        $sql = (new SqlExtractor())
            ->setTable('sales_data')
            ->setColumns(['country', 'order_date']);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    #[Test]
    public function it_can_run_a_select_query_with_connection_set(): void
    {
        $sql = (new SqlExtractor())->setConnection('testbench')
            ->setTable('sales_data')
            ->setColumns(['country', 'order_date']);
        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(getcwd() . '/tests/migrations');

        (new DatabaseFactory($this->table))->create(10);
    }
}
