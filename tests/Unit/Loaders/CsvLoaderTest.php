<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Loaders;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Loaders\CsvLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tests\TestCase;

class CsvLoaderTest extends TestCase
{
    protected Frame $frame;

    protected string $testFile;

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

        $this->testFile = $this->vfs->url() . '/csv_extractor.csv';
    }

    /** @test */
    public function it_can_create_an_instance(): void
    {
        $csv = new CsvLoader($this->testFile);

        $this->assertInstanceOf(CsvLoader::class, $csv);
    }

    /** @test */
    public function it_can_load_a_frame(): void
    {
        $csv = new CsvLoader($this->testFile);

        $csv->load($this->frame);

        $this->assertTrue(true);
    }

    public function testFileWrite(): void
    {
        $csv = new CsvLoader($this->testFile);

        $csv->load($this->frame);

        $this->assertTrue(file_exists($this->testFile));
    }
}
