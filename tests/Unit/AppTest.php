<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\EtlPipe;
use jwhulette\pipes\Loaders\CsvLoader;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;

class AppTest extends TestCase
{
    /**
     * Test the EtlPipe object gets created.
     *
     * @return void
     */
    public function testAppBoots()
    {
        $EtlPipe = new EtlPipe;
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }

    /**
     * Test extractors adding to app.
     *
     * @return void
     */
    public function testExtractorAdd()
    {
        $EtlPipe = new EtlPipe;
        $EtlPipe->extract(new CsvExtractor($this->csvExtract));
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }

    /**
     * Test extractors adding to app.
     *
     * @return void
     */
    public function testTransformsAdd()
    {
        $EtlPipe = new EtlPipe;
        $EtlPipe->extract(new CsvExtractor($this->csvExtract));
        $EtlPipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }

    public function testLoader()
    {
        $EtlPipe = new EtlPipe;
        $EtlPipe->extract(new CsvExtractor($this->csvExtract));
        $EtlPipe->transformers([
            (new CaseTransformer())->transformColumn('test', 'lower'),
        ]);
        $EtlPipe->load(new CsvLoader('test'));
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }
}
