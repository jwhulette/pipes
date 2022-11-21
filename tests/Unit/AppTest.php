<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Jwhulette\Pipes\EtlPipe;
use Jwhulette\Pipes\Extractors\CsvExtractor;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Jwhulette\Pipes\Transformers\CaseTransformer;
use Tests\TestCase;

class AppTest extends TestCase
{
    protected string $testFile = 'tests/artifacts/test_file_with_header.csv';

    public function setUp(): void
    {
    }

    /**
     * Test the EtlPipe object gets created.
     *
     * @return void
     */
    public function testAppBoots(): void
    {
        $pipe = new EtlPipe();

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    /**
     * Test extractors adding to app.
     *
     * @return void
     */
    public function testExtractorAdd(): void
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    /**
     * Test extractors adding to app.
     *
     * @return void
     */
    public function testTransformsAdd(): void
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $pipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    public function testLoader(): void
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
