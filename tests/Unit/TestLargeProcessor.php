<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\Etl;
use jwhulette\pipes\Loaders\CsvLoader;
use jwhulette\pipes\Loaders\SqlLoader;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Extractors\XlsxExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;
use jwhulette\pipes\Transformers\TrimTransformer;
use jwhulette\pipes\Transformers\DateTimeTransformer;

/**
 * This is excluded from the regular tests
 */
class TestLargeProcessor extends TestCase
{
    protected $testCsv = 'tests/files/large/50000_Sales_Records.csv';
    protected $testOutputCsv = 'tests/files/large/50000_Sales_Records_OUTPUT.csv';
    protected $testXlsx = 'tests/files/large/50000_Sales_Records.xlsx';
    protected $testOutputXlsx = 'tests/files/large/50000_Sales_Records_OUTPUT_XLSX.csv';


    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCsvProcessorLargeFile()
    {
        $etl = new Etl();
        $etl->extract(new CsvExtractor($this->testCsv));
        $etl->transforms([
            (new CaseTransformer(['Sales Channel'], 'lower')),
            (new TrimTransformer([])),
            (new DateTimeTransformer(['Order Date', 'Ship Date'])),
        ]);
        $etl->load(new CsvLoader($this->testOutputCsv));
        $etl->run();

        $this->assertTrue(true);
    }

    public function testXlsxProcessorLargeFile()
    {
        $etl = new Etl();
        $etl->extract(new XlsxExtractor($this->testXlsx));
        $etl->transforms([
            (new CaseTransformer(['Sales Channel'], 'lower')),
            (new TrimTransformer([])),
            (new DateTimeTransformer(['Order Date', 'Ship Date'])),
        ]);
        $etl->load(new CsvLoader($this->testOutputXlsx));
        $etl->run();

        $this->assertTrue(true);
    }
}
