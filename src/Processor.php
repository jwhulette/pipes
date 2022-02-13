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

    protected PipelineInterface $pipline;

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
     * @param array<TransformerInterface> $transformers
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
