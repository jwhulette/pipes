<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Tests\TestCase;
use Jwhulette\Pipes\Processor;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Jwhulette\Pipes\Extractors\CsvExtractor;
use Jwhulette\Pipes\Transformers\TrimTransformer;

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
