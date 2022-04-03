<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Jwhulette\Pipes\EtlPipe;
use Jwhulette\Pipes\Extractors\CsvExtractor;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Jwhulette\Pipes\Transformers\CaseTransformer;
use org\bovigo\vfs\vfsStream;
use Tests\TestCase;

class AppTest extends TestCase
{
    protected string $testFile;

    public function setUp(): void
    {
        $directory = [
            'csv_extractor.csv',
        ];

        $this->vfs = vfsStream::setup(sys_get_temp_dir(), null, $directory);

        $this->testFile = $this->vfs->url().'/csv_extractor.csv';
    }

    /** @test */
    public function the_app_successfully_boots()
    {
        $pipe = new EtlPipe();

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    /** @test */
    public function it_can_add_an_extractor()
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    /** @test */
    public function it_can_add_a_transformer()
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $pipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    /** @test */
    public function it_can_add_a_loader()
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $pipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);

        $pipe->load(new CsvLoader('test'));

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }
}
