<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use jwhulette\pipes\Loaders\LoaderInterface;
use jwhulette\pipes\Extractors\ExtractorInterface;

class Etl
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
     * @return \jwhulette\pipes\Etl
     */
    public function extract(ExtractorInterface $extractor): Etl
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transforms to use.
     *
     * @return \jwhulette\pipes\Etl
     */
    public function transforms(array $transformers): Etl
    {
        $this->transformers = $transformers;

        return $this;
    }

    /**
     * Set the loader to use.
     *
     * @return \jwhulette\pipes\Etl
     */
    public function load(LoaderInterface $loader): Etl
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Run the etl process.
     */
    public function run(): void
    {
        $processor = new Processor($this->extractor, $this->transformers, $this->loader);
        $processor->process();
    }
}
