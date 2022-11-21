<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Jwhulette\Pipes\Extractors\ExtractorInterface;
use Jwhulette\Pipes\Loaders\LoaderInterface;
use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;

final class Processor
{
    protected ExtractorInterface $extractor;

    protected LoaderInterface $loader;

    protected PipelineInterface $pipeline;

    /**
     * Build the pipeline.
     *
     * @param ExtractorInterface $extractor
     * @param array<int,\Jwhulette\Pipes\Transformers\TransformerInterface> $transformers
     * @param LoaderInterface $loader
     */
    public function __construct(
        ExtractorInterface $extractor,
        array $transformers,
        LoaderInterface $loader
    ) {
        $this->extractor = $extractor;
        $this->loader = $loader;
        $pipelineBuilder = new PipelineBuilder();

        $this->buildTransformerPipeline($transformers);
    }

    /**
     * Run the process.
     */
    public function process(): void
    {
        $line = $this->extractor->extract();

        foreach ($line as $collection) {
            // @phpstan-ignore-next-line
            $transformed = $this->pipeline->process($collection);

            $this->loader->load($transformed);
        }
    }

    /**
     * @param array<int,object> $transformers
     */
    private function buildTransformerPipeline(array $transformers): void
    {
        $pipelineBuilder = (new PipelineBuilder());

        foreach ($transformers as $transformer) {
            // @phpstan-ignore-next-line
            $pipelineBuilder->add($transformer);
        }

        $this->pipeline = $pipelineBuilder->build();
    }
}
