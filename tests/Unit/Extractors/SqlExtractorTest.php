<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Extractors\SqlExtractor;
use Jwhulette\Pipes\Frame;
use Tests\factories\SalesDataDatabaseFactory;
use Tests\TestCase;

class SqlExtractorTest extends TestCase
{
    protected string $table = 'sales_data';

    protected function setUp(): void
    {
        parent::setUp();
        (new SalesDataDatabaseFactory($this->table))->create(10);
    }

    /** @test */
    public function testTableConnection(): void
    {
        $sql = (new SqlExtractor())
            ->setTable($this->table);

        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    /** @test */
    public function testQueryConnection(): void
    {
        $sql = (new SqlExtractor())
            ->setTable($this->table)
            ->setSelect('country, order_date');

        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
        $this->assertInstanceOf(Collection::class, $frame->data);
        $this->assertArrayHasKey('country', $frame->data->toArray());
        $this->assertArrayHasKey('order_date', $frame->data->toArray());
        $this->assertSame(2, $frame->data->count());
    }
}
