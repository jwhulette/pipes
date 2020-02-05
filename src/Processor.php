<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;
use jwhulette\pipes\Loaders\LoaderInterface;
use jwhulette\pipes\Extractors\ExtractorInterface;

class Processor
{
    protected ExtractorInterface $extractor;
    protected LoaderInterface $loader;
    protected PipelineInterface $pipline;

    /**
     * Build the pipeline.
     *
     * @param ExtractorInterface $extractor
     * @param array<object> $transformers
     * @param LoaderInterface $loader
     */
    public function __construct(ExtractorInterface $extractor, array $transformers, LoaderInterface $loader)
    {
        $this->extractor = $extractor;
        $this->loader = $loader;
        $this->buildTransformerPipline($transformers);
    }

    /**
     * Run the process.
     */
    public function process(): void
    {
        $line = $this->extractor->extract();
        foreach ($line as $collection) {
            $transformed = $this->pipline->process($collection);
            $this->loader->load($transformed);
        }
    }

    /**
     * @param array $transformers
     */
    private function buildTransformerPipline(array $transformers): void
    {
        $piplineBuilder = (new PipelineBuilder());
        foreach ($transformers as $transformer) {
            $piplineBuilder->add($transformer);
        }

        $this->pipline = $piplineBuilder->build();
    }
}
