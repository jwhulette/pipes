<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use jwhulette\pipes\Loaders\LoaderInterface;
use jwhulette\pipes\Extractors\ExtractorInterface;

class EtlPipe
{
    /** @var \jwhulette\pipes\Extractors\ExtractorInterface */
    protected $extractor;

    /** @var \jwhulette\pipes\Loaders\LoaderInterface */
    protected $loader;

    /** @var array */
    protected $transformers = [];

    /**
     * Set the type of extractor to use.
     *
     * @param ExtractorInterface $extractor
     *
     * @return \jwhulette\pipes\EtlPipe
     */
    public function extract(ExtractorInterface $extractor): EtlPipe
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transforms to use.
     *
     * @param array $transformers
     *
     * @return \jwhulette\pipes\EtlPipe
     */
    public function transforms(array $transformers): EtlPipe
    {
        $this->transformers = $transformers;

        return $this;
    }

    /**
     * Set the loader to use.
     *
     * @param LoaderInterface $loader
     *
     * @return \jwhulette\pipes\EtlPipe
     */
    public function load(LoaderInterface $loader): EtlPipe
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Run the EtlPipe process.
     */
    public function run(): void
    {
        $processor = new Processor($this->extractor, $this->transformers, $this->loader);
        $processor->process();
    }
}
