<?php

declare(strict_types=1);

namespace jwhulette\pipes\Loaders;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use jwhulette\pipes\Frame;

class SqlLoader implements LoaderInterface
{
    protected Builder $db;

    /**
     * @var \Illuminate\Support\Collection<int,string>
     */
    protected Collection $columns;

    protected int $count = 0;

    protected int $batchSize = 500;

    /**
     * @var array<int,string|array<string,string>>
     */
    protected array $insert;

    protected bool $useColumns = false;

    public function __construct(string $table, string $connection = null)
    {
        $this->db = DB::table($table);

        if (! is_null($connection)) {
            $this->db = DB::connection($connection)->table($table);
        }
    }

    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * @param array<int,string> $columns
     *
     * @return SqlLoader
     *
     * @throws InvalidArgumentException
     */
    public function setSqlColumnNames(array $columns = []): self
    {
        $this->columns = collect($columns);

        if ($this->columns->count() === 0) {
            throw new InvalidArgumentException('SQL Columns name cannot be empty');
        }

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

    private function buildInsert(Frame $frame): void
    {
        if ($this->useColumns) {
            $this->insert[] = $this->columns->combine($frame->data)->toArray();
        } else {
            $this->insert[] = $frame->data->toArray();
        }
    }

    private function bulkInsert(): void
    {
        $this->db->insert($this->insert);
    }
}
