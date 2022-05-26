<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Contracts\TransformerInterface;

class EtlPipe
{
    protected ExtractorInterface $extractor;

    protected LoaderInterface $loader;

    /** @var array<TransformerInterface> */
    protected array $transformers = [];

    public function extract(ExtractorInterface $extractor): self
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transforms to use.
     *
     * @param array<TransformerInterface> $transformers
     *
     * @return EtlPipe
     */
    public function transformers(array $transformers): self
    {
        $this->transformers = $transformers;

        return $this;
    }

    public function load(LoaderInterface $loader): self
    {
        $this->loader = $loader;

        return $this;
    }

    public function run(): void
    {
        (new Processor(
            $this->extractor,
            $this->transformers,
            $this->loader
        ))->process();
    }
}
