<?php

namespace jwhulette\pipes\Tests\Unit\Loaders;

use Tests\TestCase;
use jwhulette\pipes\Frame;
use jwhulette\pipes\Loaders\CsvLoader;

class CsvLoaderTest extends TestCase
{
    /** @var Frame */
    protected $frame;

    /** @var string */

    protected function setUp(): void
    {
        $this->frame = new Frame();

        $this->frame->setHeader([
                'FIRSTNAME',
                'LASTNAME',
                'DOB',
            ]);

        $this->frame->setData([
                'BOB',
                'SMITH',
                '02/11/1969',
            ]);
    }

    public function testExtractorCsvInstance()
    {
        $csv = new CsvLoader($this->csvLoader);

        $this->assertInstanceOf(CsvLoader::class, $csv);
    }

    public function testHasLoader()
    {
        $csv = new CsvLoader($this->csvLoader);

        $csv->load($this->frame);

        $this->assertTrue(true);
    }

    public function testFileWritw()
    {
        $csv = new CsvLoader($this->csvLoader);

        $csv->load($this->frame);

        $this->assertTrue(file_exists($this->csvLoader));
    }
}
