<?php
declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\Etl;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Transformers\CaseTransformer;

class AppTest extends TestCase
{
    /**
     * Test the ETl object gets created
     *
     * @return void
     */
    public function testAppBoots()
    {
        $etl = new Etl;
        $this->assertInstanceOf(Etl::class, $etl);
    }

    /**
     * Test extractors adding to app
     *
     * @return void
     */
    public function testExtractorAdd()
    {
        $etl = new Etl;
        $etl->extract(new CsvExtractor($this->csvExtract));
        $this->assertInstanceOf(Etl::class, $etl);
    }

    /**
     * Test extractors adding to app
     *
     * @return void
     */
    public function testTransformsAdd()
    {
        $etl = new Etl;
        $etl->extract(new CsvExtractor($this->csvExtract));
        $etl->transforms([
            new CaseTransformer([], 'lower')
        ]);
        $this->assertInstanceOf(Etl::class, $etl);
    }
}
