<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use jwhulette\pipes\Loaders\LoaderInterface;
use jwhulette\pipes\Extractors\ExtractorInterface;

class EtlPipe
{
    protected ExtractorInterface $extractor;
    protected LoaderInterface $loader;
    protected array $transformers = [];

    /**
     * Set the type of extractor to use.
     *
     * @param ExtractorInterface $extractor
     *
     * @return EtlPipe
     */
    public function extract(ExtractorInterface $extractor): EtlPipe
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transforms to use.
     *
     * @param array<object> $transformers
     *
     * @return EtlPipe
     */
    public function transformers(array $transformers): EtlPipe
    {
        $this->transformers = $transformers;

        return $this;
    }

    /**
     * Set the loader to use.
     *
     * @param LoaderInterface $loader
     *
     * @return EtlPipe
     */
    public function load(LoaderInterface $loader): EtlPipe
    {
        $this->loader = $loader;

        return $this;
    }

    public function run(): void
    {
        (new Processor($this->extractor, $this->transformers, $this->loader))->process();
    }
}
