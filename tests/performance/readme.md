Use the datasets from the below link to test the library performance

[http://eforexcel.com/wp/downloads-18-sample-csv-files-data-sets-for-testing-sales/](https://eforexcel.com/wp/downloads-18-sample-csv-files-data-sets-for-testing-sales/)

Run `composer perf` to run the performance script

Sample runs on my notebook:

-   MacBook Pro (Retina, 15-inch, Late 2013)
-   2.3 GHz Quad-Core Intel Core i7
-   16 GB 1600 MHz DDR3

Using the following pipeline:

```php
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
```

| File                     | Peak Memory | Execution Time |
| ------------------------ | ----------- | -------------- |
| 50000 Sales Records.csv  | 1.04MB      | 1.5 seconds    |
| 100000 Sales Records.csv | 1.04MB      | 3.1 seconds    |
| 500000 Sales Records.csv | 1.04MB      | 15.3 seconds   |
