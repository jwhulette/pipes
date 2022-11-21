<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Loaders;

use Jwhulette\Pipes\Frame;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Tests\TestCase;

class CsvLoaderTest extends TestCase
{
    protected Frame $frame;

    protected string $output = 'tests/artifacts/output.csv';

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
    }

    /** @test */
    public function it_returns_a_csv_loader_instance(): void
    {
        $csv = new CsvLoader($this->output);

        $this->assertInstanceOf(CsvLoader::class, $csv);
    }

    /** @test */
    public function it_can_load_a_csv_file(): void
    {
        $csv = new CsvLoader($this->output);

        $csv->load($this->frame);

        $this->assertTrue(true);

        \unlink($this->output);
    }

    /** @test */
    public function it_can_write_a_csv_file(): void
    {
        $csv = new CsvLoader($this->output);

        $csv->load($this->frame);

        $this->assertTrue(file_exists($this->output));

        \unlink($this->output);
    }
}
