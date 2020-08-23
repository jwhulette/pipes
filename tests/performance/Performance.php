<?php

require 'vendor/autoload.php';

use jwhulette\pipes\EtlPipe;
use jwhulette\pipes\Loaders\CsvLoader;
use jwhulette\pipes\Loaders\SqlLoader;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Extractors\XmlExtractor;
use jwhulette\pipes\Extractors\XlsxExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;
use jwhulette\pipes\Transformers\TrimTransformer;
use jwhulette\pipes\Transformers\DateTimeTransformer;

function run()
{
    $filepath = 'tests/performance/files';

    if (is_readable($filepath) && count(scandir($filepath)) <= 3) {
        die('Files directory is emtpy! Unable to run performance test!');
    }

    foreach (glob($filepath.'/*.csv') as $filename) {
        echo 'Processing file: '.basename($filename).\PHP_EOL;

        $time_start = microtime(true);

        (new EtlPipe())
        ->extract(new CsvExtractor($filename))
        ->transformers([
            (new CaseTransformer())->transformColumn('Sales Channel', 'lower'),
            (new TrimTransformer())->transformAllColumns(),
            (new DateTimeTransformer())->transformColumn('Order Date')
                ->transformColumn('Ship Date'),
        ])
        ->load(new CsvLoader($filepath.'/output/output.csv'))
        ->run();

        /* Peak memory usage */
        $mem_peak = memory_get_peak_usage();

        echo 'Peak usage: '.round(($mem_peak / 1024) / 1024, 3).'MB of memory used.'.PHP_EOL;

        echo 'Total execution time in seconds: '.round(microtime(true) - $time_start, 3).PHP_EOL.PHP_EOL;
    }
}

run();
