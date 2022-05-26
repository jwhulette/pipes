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

    protected PipelineBuilder $pipelineBuilder;

    /**
     * @param array<TransformerInterface> $transformers
     */
    public function __construct(
        ExtractorInterface $extractor,
        array $transformers,
        LoaderInterface $loader
    ) {
        $this->extractor = $extractor;
        $this->loader = $loader;
        $pipelineBuilder = new PipelineBuilder();

        $this->buildTransformerPipeline($pipelineBuilder, $transformers);
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
    private function buildTransformerPipeline(PipelineBuilder $pipelineBuilder, array $transformers): void
    {
        foreach ($transformers as $transformer) {
            $pipelineBuilder->add($transformer);
        }

        $this->pipeline = $pipelineBuilder->build();
    }
}
