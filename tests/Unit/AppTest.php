<?php

declare(strict_types=1);

namespace Tests\Unit;

use Jwhulette\Pipes\EtlPipe;
use Jwhulette\Pipes\Extractors\CsvExtractor;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Jwhulette\Pipes\Transformers\CaseTransformer;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppTest extends TestCase
{
    protected string $testFile = 'tests/artifacts/test_file_with_header.csv';

    #[Override]
    public function setUp(): void
    {
    }

    #[Test]
    public function appBoots(): void
    {
        $pipe = new EtlPipe();

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    #[Test]
    public function extractorAdd(): void
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    #[Test]
    public function transformsAdd(): void
    {
        $pipe = new EtlPipe();

        $pipe->extract(new CsvExtractor($this->testFile));

        $pipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);

        $this->assertInstanceOf(EtlPipe::class, $pipe);
    }

    #[Test]
    public function loader(): void
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
