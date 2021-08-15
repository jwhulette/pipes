![Banner](.github/images/Pipes.png)

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/) [![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/run-tests?label=tests)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3Arun-tests+branch%3Amain) [![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jwhulette/factory-generator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jwhulette/factory-generator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain) [![Total Downloads](https://img.shields.io/packagist/dt/jwhulette/factory-generator.svg?style=flat-square)](https://packagist.org/packages/jwhulette/factory-generator)

# Pipes

Pipes is a PHP Extract Transform Load [ETL] package for Laravel or Laravel Zero

## Installation

You can install the package via composer:

```bash
composer require jwhulette/pipes
```

## Usage

1. Create a new EtlPipe object.

1. Add an extractor to the object to read the input file

1. Add one or more transforms to transform the data

    - You can add as many transformers as you want.

    - Data is passed to the transfomers in the order they are defined

1. Add a loader to save the data

#### Notes

-   Data is passed line by line in the pipeline using the generators

```php
$etl = new EtlPipe();
$etl->extract(new CsvExtractor('my-file.csv'));
$etl->transforms([
    new CaseTransformer()
        ->transformColumn('first_name', 'lower'),
    new TrimTransformer(),
]);
$etl->load(new CsvLoader('saved-file.csv'));
$etl->run();

or

(new EtlPipe())
    ->extract(new CsvExtractor('my-file.csv'))
    ->transforms([
        new CaseTransformer()
            ->transformColumn('first_name', 'lower'),
        new TrimTransformer(),
    ])
    ->load(new CsvLoader('saved-file.csv'))
    ->run();
```

-----------------------------------------------------------------------------

### Performance

I used the datasets from the below link to test the library performance

[http://eforexcel.com/wp/downloads-18-sample-csv-files-data-sets-for-testing-sales/](https://eforexcel.com/wp/downloads-18-sample-csv-files-data-sets-for-testing-sales/)

Sample runs on my notebook:

-   MacBook Pro (Retina, 15-inch, Late 2013)
-   2.3 GHz Quad-Core Intel Core i7
-   16 GB 1600 MHz DDR3

Using the following pipeline:

1. Transform the Sales Channel column value to lowercase
2. Trim the values in all columns
3. Format the date in the Order Date & Ship Date values

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


#### Performance tests
---- CVS -> CVS : Processing file: 100000 Sales Records.csv ----
Peak usage: 10.978MB of memory used.
Total execution time in seconds: 5.676

---- CVS -> CVS : Processing file: 1000000 Sales Records.csv ----
Peak usage: 10.978MB of memory used.
Total execution time in seconds: 59.601

---- CSV -> SQL : Processing file: 100000 Sales Records.csv ----
Peak usage: 14.229MB of memory used.
Total execution time in seconds: 6.213

---- CSV -> SQL : Processing file: 1000000 Sales Records.csv ----
Peak usage: 14.23MB of memory used.
Total execution time in seconds: 63.334

---- XLSX -> SQL : Processing file: 100000 Sales Records.xlsx ----
Peak usage: 15.371MB of memory used.
Total execution time in seconds: 35.122

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Wes Hulette](https://github.com/jwhulette)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.