<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;

final class SqlExtractor implements ExtractorInterface
{
    protected Frame $frame;

    protected QueryBuilder|Builder|null $builder = \null;

    protected ?string $connection = \null;

    protected ?string $table = \null;

    /** @var array<int,string> */
    protected ?array $select = null;

    public function __construct()
    {
        $this->frame = new Frame();
    }

    /**
     * Set a Laravel builder instance.
     */
    public function setBuilder(QueryBuilder|Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Set the database columns to query.
     *
     * @param array<int,string> $select
     *
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setColumns(array $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Set the table name to query data from.
     *
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the database connection name.
     *
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setConnection(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Extract the data from the database.
     */
    public function extract(): Generator
    {
        $db = $this->getConnection();

        foreach ($db->cursor() as $item) {
            yield $this->frame->setData((array) $item);
        }

        $this->frame->setEnd();
    }

    protected function getConnection(): QueryBuilder|Builder
    {
        if (! \is_null($this->builder)) {
            return $this->builder;
        }

        if (! \is_null($this->select) && ! \is_null($this->table)) {
            return DB::connection($this->connection)
                ->table($this->table)
                ->select($this->select);
        }

        if (\is_null($this->table)) {
            throw new \Exception('A table name has not been set', 1);
        }

        return DB::connection($this->connection)->table($this->table);
    }
}
