<?php

declare(strict_types=1);

namespace jwhulette\pipes;

use jwhulette\pipes\Extractors\ExtractorInterface;
use jwhulette\pipes\Loaders\LoaderInterface;

class EtlPipe
{
    protected ExtractorInterface $extractor;

    protected LoaderInterface $loader;

    /**
     * @var array<int,\jwhulette\pipes\Transformers\TransformerInterface>
     */
    protected array $transformers;

    /**
     * Set the type of extractor to use.
     *
     * @param ExtractorInterface $extractor
     *
     * @return EtlPipe
     */
    public function extract(ExtractorInterface $extractor): self
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transforms to use.
     *
     * @param array<int,\jwhulette\pipes\Transformers\TransformerInterface> $transformers
     *
     * @return EtlPipe
     */
    public function transformers(array $transformers): self
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
    public function load(LoaderInterface $loader): self
    {
        $this->loader = $loader;

        return $this;
    }

    public function run(): void
    {
        (new Processor($this->extractor, $this->transformers, $this->loader))->process();
    }
}
