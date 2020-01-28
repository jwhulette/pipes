<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests;

use Tests\TestCase;
use jwhulette\pipes\EtlPipe;
use Illuminate\Support\Facades\DB;
use jwhulette\pipes\Loaders\CsvLoader;
use jwhulette\pipes\Loaders\SqlLoader;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Extractors\XmlExtractor;
use jwhulette\pipes\Extractors\XlsxExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;
use jwhulette\pipes\Transformers\TrimTransformer;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use jwhulette\pipes\Transformers\DateTimeTransformer;

/**
 * This is excluded from the regular tests.
 */
class TestLargeProcessor extends TestCase
{
    protected $testCsv = 'tests/files/large/50000_Sales_Records.csv';
    protected $testOutputCsv = 'tests/files/large/50000_Sales_Records_OUTPUT.csv';
    protected $testXlsx = 'tests/files/large/50000_Sales_Records.xlsx';
    protected $testOutputXlsx
        = 'tests/files/large/50000_Sales_Records_OUTPUT_XLSX.csv';
    protected $testXML = 'tests/files/large/feed_big.xml.gz';
    protected $testOutputXml = 'tests/files/large/feed_big_OUTPUT.csv';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCsvProcessorLargeFile()
    {
        (new EtlPipe())
            ->extract(new CsvExtractor($this->testCsv))
            ->transformers([
                (new CaseTransformer())->transformColumn('Sales Channel', 'lower'),
                (new TrimTransformer())->transformAllColumns(),
                (new DateTimeTransformer())->transformColumn('Order Date')
                    ->transformColumn('Ship Date'),
            ])
            ->load(new CsvLoader($this->testOutputCsv))
            ->run();

        $this->assertTrue(true);
    }

    public function testCsvSqlProcessorLargeFile()
    {
        $columns = ['region', 'country', 'item_type', 'sales_channel', 'order_priority', 'order_date', 'order_id', 'ship_date', 'units_sold', 'unit_price', 'unit_cost', 'total_revenue', 'total_cost', 'total_profit'];

        (new EtlPipe())
            ->extract(new CsvExtractor($this->testCsv))
            ->transformers([
                (new CaseTransformer())->transformColumn('Sales Channel', 'lower'),
                (new TrimTransformer())->transformAllColumns(),
                (new DateTimeTransformer())->transformColumn('Order Date')->transformColumn('Ship Date'),
            ])
            ->load((new SqlLoader('sales_data'))->setColumns($columns))
            ->run();

        $count = DB::table('sales_data')->count();
        $this->assertEquals(50000, $count);
    }

    public function testXlsxProcessorLargeFile()
    {
        (new EtlPipe())
            ->extract(new XlsxExtractor($this->testXlsx))
            ->transformers([
                (new CaseTransformer())->transformColumn('Sales Channel', 'lower'),
                (new TrimTransformer())->transformAllColumns(),
                (new DateTimeTransformer())->transformColumn('Order Date')->transformColumn('Ship Date'),
            ])
            ->load(new CsvLoader($this->testOutputXlsx))
            ->run();

        $this->assertTrue(true);
    }

    public function testXmlProcessorLargeFile()
    {
        (new EtlPipe())
            ->extract(new XmlExtractor($this->testXML, 'prod', true))
            ->transformers([
                (new CaseTransformer())->transformColumn('brandName', 'lower'),
                (new TrimTransformer())->transformAllColumns(),
                (new DateTimeTransformer())->transformColumn('Order Date')->transformColumn('lastUpdated'),
            ])
            ->load(new CsvLoader($this->testOutputXml))
            ->run();

        $this->assertTrue(true);
    }
}
