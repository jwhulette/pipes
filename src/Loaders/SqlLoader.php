<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Loaders;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Contracts\LoaderInterface;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;

final class SqlLoader implements LoaderInterface
{
    protected Builder $db;

    /** @var \Illuminate\Support\Collection<int,string> */
    protected Collection $columns;

    protected int $count = 0;

    protected int $batchSize = 500;

    /** @var array<int,mixed> */
    protected array $insert;

    protected bool $useColumns = false;

    public function __construct(string $table, string $connection = null)
    {
        if (! is_null($connection)) {
            $this->db = DB::connection($connection)->table($table);
        }

        $this->db = DB::table($table);
    }

    /**
     * Set the size of the batch of records to insert at once.
     *
     * @param int $batchSize [Default: 500]
     *
     * @return self
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * Set the table column names.
     *
     * @param array<int,string> $columns
     *
     * @return SqlLoader
     *
     * @throws PipesInvalidArgumentException
     */
    public function setSqlColumnNames(array $columns = []): self
    {
        $this->columns = collect($columns);

        if ($this->columns->isEmpty()) {
            throw new PipesInvalidArgumentException('SQL Columns name cannot be empty');
        }

        $this->useColumns = true;

        return $this;
    }

    /**
     * Write a data frame to the database.
     *
     * @param \Jwhulette\Pipes\Frame $frame
     *
     * @return void
     */
    public function load(Frame $frame): void
    {
        $this->count++;

        $this->buildInsert($frame);

        if (($this->count >= $this->batchSize) || $frame->getEnd() === true) {
            $this->bulkInsert();

            $this->count = 0;

            $this->insert = [];
        }
    }

    private function buildInsert(Frame $frame): void
    {
        if ($this->useColumns) {
            $this->insert[] = $this->columns->combine($frame->getData())->toArray();
        } else {
            $this->insert[] = $frame->getData()->toArray();
        }
    }

    private function bulkInsert(): void
    {
        $this->db->insert($this->insert);
    }
}
