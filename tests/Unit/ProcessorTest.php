<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit;

use Jwhulette\Pipes\Extractors\CsvExtractor;
use Jwhulette\Pipes\Loaders\CsvLoader;
use Jwhulette\Pipes\Processor;
use Jwhulette\Pipes\Transformers\TrimTransformer;
use Tests\TestCase;

class ProcessorTest extends TestCase
{
    /** @test */
    public function it_can_create_a_new_processor(): void
    {
        $extractor = new CsvExtractor('test');
        $transforms = [
            (new TrimTransformer()),
        ];
        $loader = new CsvLoader('test');
        $processor = new Processor($extractor, $transforms, $loader);

        $this->assertInstanceOf(Processor::class, $processor);
    }
}
