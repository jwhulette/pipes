<?php

namespace Tests\Unit\Extractors;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Jwhulette\Pipes\Exceptions\PipesException;
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

    /**
     * @test
     */
    public function testTableConnection()
    {
        $sql = (new SqlExtractor())
            ->setTable($this->table);

        $frameData = $sql->extract();
        $frame = $frameData->current();

        $this->assertInstanceOf(Frame::class, $frame);
    }

    /**
     * @test
     */
    public function testQueryConnection()
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

    /**
     * @test
     */
    public function testDatabaseConnectionThrowsError()
    {
        $this->expectException(PipesException::class);

        $sql = (new SqlExtractor());

        $frameData = $sql->extract();
        $frameData->current();
    }
}
