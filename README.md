![Banner](.github/images/pipes_banner.png)

![Build](https://github.com/jwhulette/pipes/workflows/Tests/badge.svg)

# Pipes

Pipes is a PHP Extract Transform Load [ETL] package for Laravel or Laravel Zero

This a currently a work in progress, my idea was to have it as a "plug-in" for Laravel Zero

## Installation

```bash
composer require jwhulette/pipes
```

## Usage

Create a new EtlPipe object.

Add an extractor to the object to read the file

You can add as many transformers as you want.

Add a loader to save the data

### Notes

Data is passed line by line from the loader using the generator function `yeild`

Data is passed to the transfomers in the order they are defined

```php
$etl = new EtlPipe();
$etl->extract(new CsvExtractor($this->csvFile));
$etl->transforms([
    new CaseTransformer([], 'lower'),
    new TrimTransformer(),
]);
$etl->load(new CsvLoader('saved-file.csv'));
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
