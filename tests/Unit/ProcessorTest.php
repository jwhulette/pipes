<?php

declare(strict_types=1);

namespace jwhulette\pipes\Tests\Unit;

use Tests\TestCase;
use jwhulette\pipes\Processor;
use jwhulette\pipes\Loaders\CsvLoader;
use jwhulette\pipes\Extractors\CsvExtractor;
use jwhulette\pipes\Transformers\TrimTransformer;

class ProcessorTest extends TestCase
{
    public function testProcessorConstruction()
    {
        $extractor = new CsvExtractor('test');
        $transforms = [
            (new TrimTransformer),
        ];
        $loader = new CsvLoader('test');
        $processor = new Processor($extractor, $transforms, $loader);

        $this->assertInstanceOf(Processor::class, $processor);
    }
}
