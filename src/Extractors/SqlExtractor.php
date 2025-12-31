<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Exception;
use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use function is_null;
use Jwhulette\Pipes\Contracts\ExtractorInterface;
use Jwhulette\Pipes\Frame;
use Throwable;

final class SqlExtractor implements ExtractorInterface
{
    protected Frame $frame;

    /** @var QueryBuilder|Builder<Model>|null */
    protected QueryBuilder|Builder|null $builder = null;

    protected ?string $connection = null;

    protected ?string $table = null;

    /** @var array<int,string> */
    protected ?array $select = null;

    public function __construct()
    {
        $this->frame = new Frame();
    }

    /**
     * Set a Laravel builder instance.
     *
     * @param  QueryBuilder|Builder<Model>  $builder
     */
    public function setBuilder(QueryBuilder|Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Set the database columns to query.
     *
     * @param  array<int,string>  $select
     *
     * @return SqlExtractor
     */
    public function setColumns(array $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Set the table name to query data from.
     *
     * @param  string  $table
     *
     * @return SqlExtractor
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Extract the data from the database.
     *
     * @throws Throwable
     */
    public function extract(): Generator
    {
        $db = $this->builder;

        // If no builder was set, create one.
        if (is_null($db)) {
            $db = $this->getConnection();
        }

        foreach ($db->cursor() as $item) {
            /** @var list<bool|float|int|string|null> $itemArray */
            $itemArray = (array) $item;

            yield $this->frame->setData(
                $itemArray
            );
        }

        $this->frame->setEnd();
    }

    /**
     * @throws Throwable
     */
    protected function getConnection(): QueryBuilder
    {
        if (! is_null($this->select) && ! is_null($this->table)) {
            return DB::connection($this->connection)
                ->table($this->table)
                ->select($this->select);
        }

        throw_if(
            is_null($this->table),
            Exception::class,
            'A table name has not been set'
        );

        return DB::connection($this->connection)->table($this->table);
    }

    /**
     * Set the database connection name.
     *
     * @param  string  $connection
     *
     * @return SqlExtractor
     */
    public function setConnection(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }
}
