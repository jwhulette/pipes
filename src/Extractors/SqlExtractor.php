<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Jwhulette\Pipes\Extractors\ExtractorInterface;
use Jwhulette\Pipes\Frame;

class SqlExtractor implements ExtractorInterface
{
    protected DB $db;

    protected Frame $frame;

    protected ?string $connection = null;

    protected ?string $table = null;

    protected ?string $select = null;

    public function __construct()
    {
        $this->frame = new Frame;
    }

    /**
     * Set the database select statement.
     * @param string $select
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setSelect(string $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Set the table name to query data from.
     * @param string $table
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setTable(string $table):self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the database connection name.
     * @param string $connection
     * @return \Jwhulette\Pipes\Extractors\SqlExtractor
     */
    public function setConnection(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function extract(): Generator
    {
        $db = $this->getConnection();

        if (\is_null($db)) {
            throw new \Exception('Database connection unavailable', 1);
        }

        foreach ($db->cursor() as $item) {
            yield $this->frame->setData((array) $item);
        }

        $this->frame->setEnd();
    }

    /**
     * @return \Illuminate\Database\Query\Builder|null
     */
    protected function getConnection(): ?Builder
    {
        if (! is_null($this->connection)) {
            DB::setDefaultConnection($this->connection);
        }

        if (! is_null($this->select) && ! is_null($this->table)) {
            return DB::table($this->table)->selectRaw($this->select);
        }

        if (! is_null($this->table)) {
            return DB::table($this->table);
        }

        return null;
    }
}
