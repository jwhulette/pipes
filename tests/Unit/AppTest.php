<?php
declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\EtlPipe;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;

class AppTest extends TestCase
{
    /**
     * Test the EtlPipe object gets created
     *
     * @return void
     */
    public function testAppBoots()
    {
        $EtlPipe = new EtlPipe;
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }

    /**
     * Test extractors adding to app
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
     * Test extractors adding to app
     *
     * @return void
     */
    public function testTransformsAdd()
    {
        $EtlPipe = new EtlPipe;
        $EtlPipe->extract(new CsvExtractor($this->csvExtract));
        $EtlPipe->transforms([
            new CaseTransformer([], 'lower')
        ]);
        $this->assertInstanceOf(EtlPipe::class, $EtlPipe);
    }
}
