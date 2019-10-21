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

class ProcessorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete();
    }

    public function testCsvProcessorLargeFile()
    {
        $etl = new Etl();
        $etl->extract(new CsvExtractor('tests/files/large files/50000 Sales Records.csv'));
        // $etl->extract(new CsvExtractor('tests/files/large files/1000000 Sales Records.csv'));

        $etl->transforms([
            (new CaseTransformer(['Sales Channel'], 'lower')),
            (new TrimTransformer([])),
            (new DateTimeTransformer(['Order Date', 'Ship Date'])),
        ]);
        // $etl->load(new CsvLoader('tests/files/large files/test_loader_50000.csv'));
        $etl->load(new SqlLoader('test'));

        $etl->run();

        $this->assertTrue(true);
    }

    public function testXlsxProcessorLargeFile()
    {
        $etl = new Etl();
        $etl->extract(new XlsxExtractor('tests/files/large files/50000 Sales Records.xlsx'));
        $etl->transforms([
            (new CaseTransformer(['Sales Channel'], 'lower')),
            (new TrimTransformer([])),
            (new DateTimeTransformer(['Order Date', 'Ship Date'])),
        ]);
        $etl->load(new CsvLoader('tests/files/large files/test_loader_50000_xlsx.csv'));
        $etl->run();

        $this->assertTrue(true);
    }
}
