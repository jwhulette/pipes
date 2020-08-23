![Banner](.github/images/pipes_banner.png)

![Build](https://github.com/jwhulette/pipes/workflows/Tests/badge.svg)

# Pipes

Pipes is a PHP Extract Transform Load [ETL] package for Laravel or Laravel Zero

## Installation

Add the following to your composer.json file.

    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/jwhulette/pipes"
        }
    ],
    "require": {
        "jwhulette/pipes": "master",
    }

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

##############################################################################

### Performance

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
| 50000 Sales Records.csv  | 1.043MB     | 1.556 seconds  |
| 100000 Sales Records.csv | 1.043MB     | 3.063 seconds  |
| 500000 Sales Records.csv | 1.043MB     | 15.898 seconds |

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
