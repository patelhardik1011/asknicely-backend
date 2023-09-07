<?php
/*
 * This is the base model. All other models extend this model.
 */

namespace App\core\database;

use App\core\App;
use RuntimeException;
use Exception;

/**
 * Class Model
 * @package App\core\database
 */
abstract class Model
{

    /**
     * The table name for the Model.
     * @var string
     */
    protected static string $table = '';

    /**
     * The ID for the Model.
     * @var int
     */
    protected int $id = 0;

    /**
     * The columns for the Model.
     * @var array
     */
    protected array $cols = [];

    /**
     * The rows for the Model.
     * @var array
     */
    protected array $rows = [];

    /**
     * This method returns the last SQL query by the query builder.
     * @return string
     * @throws Exception
     */
    public function getSql(): string
    {
        return App::DB()->setClassName(get_class($this))->getSql();
    }

    public function describe(): array
    {
        if (!$this->cols) {
            $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        }
        return $this->cols;
    }

    /**
     * This method finds one or more rows in the database based off of ID and binds it to the Model, or returns null if no rows are found.
     * @param $id
     * @return $this
     * @throws Exception
     */
    public function find($id): ?Model
    {
        $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        $this->rows = App::DB()->setClassName(get_class($this))->selectAllWhere(static::$table, [[$this->cols[0]->Field, '=', $id]]);
        return !empty($this->rows) ? $this : null;
    }

    /**
     * This method finds one or more rows in the database based off of ID and binds it to the Model, or throws an exception if no rows are found.
     * @param $id
     * @return $this
     * @throws Exception
     */
    public function findOrFail($id): Model
    {
        $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        $this->rows = App::DB()->setClassName(get_class($this))->selectAllWhere(static::$table, [[$this->cols[0]->Field, '=', $id]]);
        if (!empty($this->rows)) {
            return $this;
        }
        throw new RuntimeException("ModelNotFoundException");
    }

    /**
     * This method finds one or more rows matching specific criteria in the database and binds it to the Model, then returns the Model.
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return $this
     * @throws Exception
     */
    public function where($where, $limit = "", $offset = ""): Model
    {
        $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        $this->rows = App::DB()->setClassName(get_class($this))->selectAllWhere(static::$table, $where, $limit, $offset);
        return $this;
    }

    /**
     * This method returns the count of the rows for a database query.
     * @param $where
     * @return int|bool
     * @throws Exception
     */
    public function count($where = "")
    {
        if (!empty($where)) {
            return App::DB()->setClassName(get_class($this))->countWhere(static::$table, $where);
        }
        return App::DB()->setClassName(get_class($this))->count(static::$table);
    }

    /**
     * This method adds the row to the database and binds it to the model.
     * @param $columns
     * @return $this
     * @throws Exception
     */
    public function add($columns): Model
    {
        $this->id = App::DB()->insert(static::$table, $columns);
        $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        $this->rows = App::DB()->setClassName(get_class($this))->selectAllWhere(static::$table, [[$this->cols[0]->Field, '=', $this->id]]);
        return $this;
    }

    /**
     * This method updates one or more rows in the database matching specific criteria.
     * @param $parameters
     * @param $where
     * @return int
     * @throws Exception
     */
    public function updateWhere($parameters, $where): int
    {
        return App::DB()->updateWhere(static::$table, $parameters, $where);
    }

    /**
     * This static method returns and binds one or more rows in the database to the model.
     * @return Model[]|false
     * @throws Exception
     */
    public function all()
    {
        return App::DB()->setClassName(static::class)->selectAll(static::$table);
    }

    /**
     * This method fetches all the rows for the Model.
     * @return Model[]
     */
    public function get(): array
    {
        return $this->rows;
    }

    /**
     * This method fetches the first row for the Model.
     * @return Model|null
     */
    public function first(): ?Model
    {
        return $this->rows[0] ?? null;
    }

    /**
     * This method returns the primary key's value for the Model, or null if it doesn't have one.
     * @return string|null
     * @throws Exception
     */
    public function id(): ?string
    {
        if (!$this->cols) {
            $this->cols = App::DB()->setClassName(get_class($this))->describe(static::$table);
        }
        return $this->{$this->cols[0]->Field} ?? null;
    }

    /**
     * @throws Exception
     */
    public function createTable(array $columns = [])
    {
        App::DB()->setClassName(get_class($this))->createTable(static::$table, $columns);
        return App::DB()->setClassName(get_class($this))->describe(static::$table);
    }

    public function raw(string $sql = '', array $parameters = [])
    {
        return App::DB()->setClassName(get_class($this))->raw($sql, $parameters);
    }
}