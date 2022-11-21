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

Add an extractor to the object to read a file or database.

You can add as many transformers as you want.

Add a loader to save the data

### Notes
**Built-in extractors:**
* CsvExtractor
* XlsxExtractor
* SqlExtractor

**Built-in loaders:**
* CsvLoader
* SqlLoader

**Built-in transformers:**
*CaseTransformer - Change the case of a string
*DateTimeTransformer - Change the format of a date string
*PhoneTransformer - Transform a US phone, removing all non numeric characters, and limiting the length to the first 10 digits
*TrimTransformer - Trim a string
*ZipcodeTransformer - Transform a US zip code, removing all non numeric characters, and left pad zeros for zip codes less than 5 digits
*ConditionalTransformer - Transform a column, based on the values of another column

*Data is passed to the transformers in the order they are defined*

```php
(new EtlPipe())
->extract(new CsvExtractor($this->csvFile));
->transforms([
    new CaseTransformer([], 'lower'),
    new TrimTransformer(),
])
->load(new CsvLoader('saved-file.csv'));
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
