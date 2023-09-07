<?php

namespace App\core\database;

use App\core\App;
use PDO;
use PDOException;
use PDOStatement;
use Exception;

class QueryBuilder
{
    /**
     * This is the PDO instance.
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * This is the class name a Model will be bound to.
     * @var
     */
    protected $className;

    /**
     * This is the current SQL query.
     * @var string
     */
    protected string $sql;

    /**
     * This method is the constructor for the QueryBuilder class and simply initializes a new PDO object.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * This method returns the PDO instance.
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * This method returns the last set SQL query.
     *
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * This method sets the class name to bind the Model to.
     * @param mixed $className
     * @return QueryBuilder
     */
    public function setClassName($className): QueryBuilder
    {
        $this->className = $className;
        return $this;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectAll(string $table, string $limit = "", string $offset = "")
    {
        return $this->select($table, "*", $limit, $offset);
    }

    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectAllWhere(string $table, $where, string $limit = "", string $offset = "")
    {
        return $this->selectWhere($table, "*", $where, $limit, $offset);
    }

    /**
     * This method returns the number of rows in a table.
     * @param string $table
     * @return  int|bool
     * @throws Exception
     */
    public function count(string $table)
    {
        $this->sql = "SELECT COUNT(*) FROM {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method returns the number of rows in a table where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $columns
     * @return int|bool
     * @throws Exception
     */
    public function countWhere(string $table, $where)
    {
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "SELECT COUNT(*) FROM {$table} WHERE {$mapped_wheres}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method selects rows from a table in a database.
     * @param string $table
     * @param string $columns
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function select(string $table, string $columns, string $limit = "", string $offset = "")
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $this->sql = "SELECT {$columns} FROM {$table} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->className ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param string $columns
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectWhere(string $table, string $columns, $where, string $limit = "", string $offset = "")
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "SELECT {$columns} FROM {$table} WHERE {$mapped_wheres} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->className ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method inserts data into a table in a database.
     * @param string $table
     * @param $parameters
     * @return string
     * @throws Exception
     */
    public function insert(string $table, $parameters): string
    {
        $names = $this->prepareCommaSeparatedColumnNames($parameters);
        $values = $this->prepareCommaSeparatedColumnValues($parameters);
        $this->sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            $names,
            $values
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return "";
    }

    /**
     * This method updates data in a table in a database.
     * @param string $table
     * @param $parameters
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function update(string $table, $parameters, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareNamed($parameters);
        $this->sql = sprintf(
            'UPDATE %s SET %s %s',
            $table,
            $set,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method updates data in a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $parameters
     * @param $where
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function updateWhere(string $table, $parameters, $where, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareUnnamed($parameters);
        $parameters = $this->prepareParameters($parameters);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = sprintf(
            'UPDATE %s SET %s WHERE %s %s',
            $table,
            $set,
            $mapped_wheres,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute(array_merge($parameters, $where));
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @return array|int
     * @throws Exception
     */
    public function describe(string $table)
    {
        $this->sql = "DESCRIBE {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->className ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method executes raw SQL against a database.
     * @param string $sql
     * @param array $parameters
     * @return array|int
     * @throws Exception
     */
    public function raw(string $sql, array $parameters = [])
    {
        try {
            $this->sql = $sql;
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
            $output = $statement->rowCount();
            if (stripos($sql, "SELECT") === 0) {
                $output = $statement->fetchAll(PDO::FETCH_CLASS, $this->className ?: "stdClass");
            }
            return $output;
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    public function createTable(string $tableName = '', array $columns = [])
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $tableName . "( id INT(10) AUTO_INCREMENT,";
        $columnsArray = [];
        foreach ($columns as $column) {
            $columnsArray[] = $column . " VARCHAR(100) NULL";
        }
        $sql .= implode(',', $columnsArray) . ", PRIMARY KEY(id));";
        try {
            $statement = $this->pdo->prepare($sql);
            return $statement->execute();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
    }

    /**
     * This method prepares the where clause array for the query builder.
     * @param $where
     * @return mixed
     */
    private function prepareWhere($where)
    {
        $array = $where;
        foreach ($where as $key => $value) {
            if (count($value) < 4) {
                array_unshift($array[$key], 0);
            }
        }
        return $array;
    }

    /**
     * This method prepares the limit statement for the query builder.
     * @param $limit
     * @return string
     */
    private function prepareLimit($limit): string
    {
        return (!empty($limit) ? " LIMIT " . $limit : "");
    }

    /**
     * This method prepares the offset statement for the query builder.
     * @param $offset
     * @return string
     */
    private function prepareOffset($offset): string
    {
        return (!empty($offset) ? " OFFSET " . $offset : "");
    }

    /**
     * This method prepares the comma separated names for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnNames($parameters): string
    {
        return implode(', ', array_keys($parameters));
    }

    /**
     * This method prepares the comma separated values for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnValues($parameters): string
    {
        return ':' . implode(', :', array_keys($parameters));
    }

    /**
     * This method prepares the mapped wheres.
     * @param $where
     * @return string
     */
    private function prepareMappedWheres($where): string
    {
        $mapped_wheres = '';
        foreach ($where as $clause) {
            $modifier = $mapped_wheres === '' ? '' : $clause[0];
            $mapped_wheres .= " {$modifier} {$clause[1]} {$clause[2]} ?";
        }
        return $mapped_wheres;
    }

    /**
     * This method prepares the unnamed columns.
     * @param $parameters
     * @return string
     */
    private function prepareUnnamed($parameters): string
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = ?";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the named columns.
     * @param $parameters
     * @return string
     */
    private function prepareNamed($parameters): string
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = :{$property}";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the parameters with numeric keys.
     * @param $parameters
     * @param int $counter
     * @return mixed
     */
    private function prepareParameters($parameters, int $counter = 1)
    {
        foreach ($array = $parameters as $key => $value) {
            unset($parameters[$key]);
            $parameters[$counter] = $value;
            $counter++;
        }
        return $parameters;
    }

    /**
     * This method binds values from an array to the PDOStatement.
     * @param PDOStatement $PDOStatement
     * @param $array
     * @param int $counter
     */
    private function prepareBindings(PDOStatement $PDOStatement, $array, int $counter = 1): void
    {
        foreach ($array as $key => $value) {
            $PDOStatement->bindParam($counter, $value);
            $counter++;
        }
    }

    /**
     * This method handles PDO exceptions.
     * @param PDOException $e
     * @return void
     * @throws Exception
     */
    private function handlePDOException(PDOException $e)
    {
        error_log('There was a PDO Exception. Details: ' . $e->getMessage());
        return $e->getMessage();
    }
}