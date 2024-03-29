<?php

declare(strict_types=1);

namespace Jwhulette\Pipes;

use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Contracts\TransformerInterface;

final class EtlPipe
{
    protected ExtractorInterface $extractor;

    protected LoaderInterface $loader;

    /**
     * @var array<int,TransformerInterface>
     */
    protected array $transformers;

    /**
     * Set the type of extractor to use.
     */
    public function extract(ExtractorInterface $extractor): self
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Set the transformers to use.
     *
     * @param array<int,TransformerInterface> $transformers
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
     * @return EtlPipe
     */
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
