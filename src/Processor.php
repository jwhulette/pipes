<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use League\Pipeline\PipelineBuilder;
use jwhulette\pipes\Loaders\LoaderInterface;
use jwhulette\pipes\Extractors\ExtractorInterface;

class Processor
{
    /** @var \jwhulette\pipes\Extractors\ExtractorInterface */
    protected $extractor;

    /** @var \jwhulette\pipes\Loaders\LoaderInterface */
    protected $loader;

    /** @var \League\Pipeline\PipelineInterface */
    protected $pipline;

    /**
     * Processor Extractor
     *
     * @param \jwhulette\pipes\Extractors\ExtractorInterface $extractor
     * @param array $transformers
     * @param \jwhulette\pipes\Loaders\LoaderInterface $loader
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
     * Build a transformer pipline.
     *
     * @param array $transfromers
     */
    private function buildTransformerPipline(array $transfromers): void
    {
        $piplineBuilder = (new PipelineBuilder());
        foreach ($transfromers as $transformer) {
            $piplineBuilder->add($transformer);
        }

        $this->pipline = $piplineBuilder->build();
    }
}
