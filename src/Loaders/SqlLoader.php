<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use jwhulette\pipes\Frame;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class SqlLoader implements LoaderInterface
{
    protected Builder $db;
    protected Collection $columns;
    protected int $count = 0;
    protected int $batchSize = 500;
    protected array $insert = [];
    protected bool $useColumns = false;

    /**
     * @param string $table
     * @param string $connection
     */
    public function __construct(string $table, string $connection = null)
    {
        $this->db = DB::table($table);
        if (! is_null($connection)) {
            $this->db = DB::connection($connection)->table($table);
        }
    }

    /**
     * @param int $batchSize
     *
     * @return SqlLoader
     */
    public function setBatchSize(int $batchSize): SqlLoader
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return SqlLoader
     */
    public function setSqlColumnNames(array $columns = []): SqlLoader
    {
        $this->columns = collect($columns);
        $this->useColumns = true;

        return $this;
    }

    /**
     * @param Frame $frame
     */
    public function load(Frame $frame): void
    {
        $this->count++;

        $this->buildInsert($frame);

        if (($this->count >= $this->batchSize) || $frame->end === true) {
            $this->bulkInsert();

            $this->count = 0;
            $this->insert = [];
        }
    }

    /**
     * @param Frame $frame
     */
    private function buildInsert(Frame $frame): void
    {
        if ($this->useColumns) {
            $this->insert[] = $this->columns->combine($frame->data)->toArray();
        } else {
            $this->insert[] = $frame->data->toArray();
        }
    }

    /**
     * Bulk insert the data.
     */
    private function bulkInsert(): void
    {
        $this->db->insert($this->insert);
    }
}
