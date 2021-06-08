<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use Jwhulette\Pipes\Frame;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Jwhulette\Pipes\Exceptions\PipesException;
use Jwhulette\Pipes\Extractors\ExtractorInterface;

class SqlExtractor extends Extractor implements ExtractorInterface
{
    protected DB $db;
    protected ?string $connection = null;
    protected ?string $table = null;
    protected ?string $select = null;

    public function __construct()
    {
        $this->frame = new Frame;
    }

    /**
     * @param string $select The raw select query to use
     *
     * @return SqlExtractor
     */
    public function setSelect(string $select): SqlExtractor
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @param string $table The name of the table to use
     *
     * @return SqlExtractor
     */
    public function setTable(string $table): SqlExtractor
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param string $connection The database connection to use
     *
     * @return SqlExtractor
     */
    public function setConnection(string $connection): SqlExtractor
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Generator
     */
    public function extract(): Generator
    {
        $db = $this->getConnection();

        foreach ($db->lazyById() as $item) {
            yield $this->frame->setData((array) $item);
        }

        $this->frame->setEnd();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     *
     * @throws PipesException
     */
    protected function getConnection(): Builder
    {
        if (\is_null($this->connection) === \false) {
            DB::setDefaultConnection($this->connection);
        }

        if (\is_null($this->select) === \false) {
            return DB::table($this->table)->selectRaw($this->select);
        }

        if (\is_null($this->table) === \false) {
            return DB::table($this->table);
        }

        throw new PipesException('No valid database configuration found');
    }
}
