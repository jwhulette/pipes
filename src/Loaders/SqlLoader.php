<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use jwhulette\pipes\Frame;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SqlLoader implements LoaderInterface
{
    /** @var int */
    protected $count = 0;

    /** @var int */
    protected $batchSize = 100;

    /** @var array */
    protected $insert = [];

    /** @var Collection */
    protected $columns;

    /** @var bool */
    protected $useColumns;

    /** @var \Illuminate\Database\Query\Builder */
    protected $db;

    /**
     * __construct.
     *
     * @param string $table
     * @param string $connection
     */
    public function __construct(string $table, string $connection = null)
    {
        if (!is_null($connection)) {
            $this->db = DB::connection($connection)->table($table);
        } else {
            $this->db = DB::table($table);
        }
    }

    /**
     * Set the batch size.
     *
     * @param int $batchSize
     *
     * @return \jwhulette\pipes\Loaders\SqlLoader
     */
    public function setBatchSize(int $batchSize): SqlLoader
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * Set the column names for the insert.
     *
     * @param array $columns
     *
     * @return \jwhulette\pipes\Loaders\SqlLoader
     */
    public function setColumns(array $columns = []): SqlLoader
    {
        $this->columns = collect($columns);

        $this->useColumns = count($this->columns) > 0 ? true : false;

        return $this;
    }

    /**
     * Write the data to the loader.
     *
     * @param \jwhulette\pipes\Frame $frame
     */
    public function load(Frame $frame): void
    {
        ++$this->count;
        
        $this->buildInsert($frame);

        if (($this->count >= $this->batchSize) || $frame->end === true) {
            $this->bulkInsert();

            $this->count = 0;

            $this->insert = [];
        }
    }

    /**
     * Add custom array keys for the column names.
     *
     * @param \jwhulette\pipes\Frame $frame
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
