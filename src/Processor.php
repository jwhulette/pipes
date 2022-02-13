<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;

/**
 * Processor.
 */
class Processor
{
    protected ExtractorInterface $extractor;

    protected LoaderInterface $loader;
    protected PipelineInterface $pipeline;

    /**
     * Build the pipeline.
     *
     * @param ExtractorInterface $extractor
     * @param array<TransformerInterface> $transformers
     * @param LoaderInterface $loader
     */
    public function __construct(ExtractorInterface $extractor, array $transformers, LoaderInterface $loader)
    {
        $this->extractor = $extractor;

        $this->loader = $loader;

        $this->buildTransformerPipeline($transformers);
    }

    /**
     * Run the process.
     */
    public function process(): void
    {
        $line = $this->extractor->extract();

        foreach ($line as $collection) {
            $transformed = $this->pipeline->process($collection);

            $this->loader->load($transformed);
        }
    }

    /**
     * @param array<TransformerInterface> $transformers
     */
    private function buildTransformerPipeline(array $transformers): void
    {
        $pipelineBuilder = (new PipelineBuilder());

        foreach ($transformers as $transformer) {
            $pipelineBuilder->add($transformer);
        }

        $this->pipeline = $pipelineBuilder->build();
    }
}
