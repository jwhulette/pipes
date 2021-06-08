<?php

namespace Jwhulette\Pipes\Tests\Unit\Loaders;

use Tests\TestCase;
use Jwhulette\Pipes\Frame;
use org\bovigo\vfs\vfsStream;
use Jwhulette\Pipes\Loaders\CsvLoader;
use org\bovigo\vfs\vfsStreamDirectory;

class CsvLoaderTest extends TestCase
{
    protected Frame $frame;
    protected string $testfile;
    protected vfsStreamDirectory $vfs;

    protected function setUp(): void
    {
        $this->frame = new Frame();

        $this->frame->setData([
            'BOB',
            'SMITH',
            '02/11/1969',
        ]);

        $this->frame->setHeader([
                'FIRSTNAME',
                'LASTNAME',
                'DOB',
            ]);

        $directory = [
                'csv_extractor.csv',
            ];

        $this->vfs = vfsStream::setup(sys_get_temp_dir(), null, $directory);

        $this->testfile = $this->vfs->url().'/csv_extractor.csv';
    }

    public function testExtractorCsvInstance()
    {
        $csv = new CsvLoader($this->testfile);

        $this->assertInstanceOf(CsvLoader::class, $csv);
    }

    public function testHasLoader()
    {
        $csv = new CsvLoader($this->testfile);

        $csv->load($this->frame);

        $this->assertTrue(true);
    }

    public function testFileWrite()
    {
        $csv = new CsvLoader($this->testfile);

        $csv->load($this->frame);

        $this->assertTrue(file_exists($this->testfile));
    }
}
