<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Illuminate\Database\Query\Builder;
use Jwhulette\Pipes\Contracts\Extractor;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Exceptions\PipesSqlException;
use Jwhulette\Pipes\Frame;

class SqlExtractor extends Extractor implements ExtractorInterface
{
    protected Builder $queryBuilder;

    public function __construct()
    {
        $this->frame = new Frame();
    }

    /**
     * Set a laravel Query Builder.
     *
     * @param \Illuminate\Database\Query\Builder $queryBuilder
     *
     * @return SqlExtractor
     */
    public function setQueryBuilder(Builder $queryBuilder): SqlExtractor
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * @return Generator
     *
     * @throws PipesSqlException
     */
    public function extract(): Generator
    {
        try {
            foreach ($this->queryBuilder->lazyById() as $item) {
                yield $this->frame->setData((array) $item);
            }
        } catch (\Throwable $th) {
            throw new PipesSqlException($th->getMessage());
        }

        $this->frame->setEnd();
    }
}
