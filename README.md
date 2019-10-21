# Pipes

Pipes is a PHP Extract Transform Load [ETL] package for Laravel or Laravel Zero

This a currently a work in progress, my idea was to have it as a "plug-in" for Laravel Zero

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

Create a new Etl object.  

Add an extractor to the object

You can add as many transformers as you want.

Add a loader to save the data

### Notes

Data is passed line by line from the loader using the generator function `yeild`

Data is passed to the transfomers in the order they are defined 

```php
$etl = new Etl;
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