<?php

namespace Smll\ORM;

use ICanBoogie\Inflector;
use Smll\Database\Database;

abstract class Model
{
    protected $table;
    protected $pagination = 20;
    protected $attributes = [];

    public function __construct()
    {
        if (empty($this->table)) {
            $inflector = Inflector::get();
            $this->table = strtolower($inflector->pluralize(substr(strrchr(get_called_class(), "\\"), 1)));
        }

        echo $this->table;
    }

    public function __get($name)
    {
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * Get objects with given attributes
     * @return object
     */
    public static function get()
    {
        $select = func_get_args();
        $select = implode(', ', $select);
        return (new static)->getQuery($select);
    }

    protected function getQuery($select)
    {
        return Database::table($this->table)->select($select)->get();
    }

    /**
     * Join Table
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return object
     */
    public static function join(string $table, string $first, string $operator, string $second, $type = "INNER")
    {
        return (new static)->joinQuery($table, $first, $operator, $second, $type);
    }

    protected function joinQuery($table, $first, $operator, $second, $type)
    {
        return Database::table($this->table)->join($table, $first, $operator, $second, $type);
    }

    /**
     * Right Join Table
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return object
     */
    public static function rightJoin(string $table, string $first, string $operator, string $second)
    {
        return (new static)->rightjoinQuery($table, $first, $operator, $second);
    }

    protected function rightjoinQuery(string $table, string $first, string $operator, string $second)
    {
        return Database::table($this->table)->rightJoin($table, $first, $operator, $second);
    }

    /**
     * Left Join Table
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return object
     */
    public static function leftJoin(string $table, string $first, string $operator, string $second)
    {
        return(new static)->leftjoinQuery($table, $first, $operator, $second);

    }

    protected function leftjoinQuery(string $table, string $first, string $operator, string $second)
    {
        return Database::table($this->table)->leftJoin($table, $first, $operator, $second);
    }

    /**
     * Where data
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $type
     * @return object
     */
    public static function where(string $column, string $operator, string $value, string $type = null)
    {
        return (new static)->whereQuery($column, $operator, $value, $type);
    }

    protected function whereQuery($column, $operator, $value, $type)
    {
        return Database::table($this->table)->where($column, $operator, $value, $type);
    }

    /**
     * OR where data
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return object
     */
    public static function orWhere(string $column, string $operator, string $value)
    {
        return (new static)->orWhereQuery($column, $operator, $value, "OR");
    }

    protected function orWhereQuery($column, $operator, $value, $type)
    {
        return Database::table($this->table)->orWhere($column, $operator, $value, $type);
    }

    /**
     * Group by data
     * @return object
     */
    public static function groupBy()
    {
        $group_by = func_get_args();
        $group_by = "GROUP BY " . implode(', ', $group_by) . " ";

        return (new static)->groupByQuery($group_by);
    }

    protected function groupByQuery($group_by)
    {
        return Database::table($this->table)->groupBy($group_by);
    }

    /**
     * having data
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return object
     */
    public static function having(string $column, string $operator, string $value)
    {
        return (new static)->havingQuery($column, $operator, $value);
    }

    protected function havingQuery($column, $operator, $value)
    {
        return Database::table($this->table)->having($column, $operator, $value);
    }

    /**
     * order by data
     * @param string $column
     * @param string $type
     * @return object
     */
    public static function orderBy(string $column, string $type = null)
    {
        return (new static)->orderByQuery($column, $type);
    }

    protected function orderByQuery($column, $type)
    {
        return Database::table($this->table)->orderBy($column, $type);
    }

    /**
     * Limit
     * @param $limit
     * @return Database|string
     */
    public static function limit($limit)
    {
        return (new static)->limitQuery($limit);
    }

    protected function limitQuery($limit)
    {
        return Database::table($this->table)->limit($limit);
    }

    /**
     * Offset
     * @param $offset
     * @return Database|string
     */
    public static function offset($offset)
    {
        return (new static)->offsetQuery($offset);
    }

    protected function offsetQuery($offset)
    {
        return Database::table($this->table)->offset($offset);
    }

    /**
     * Get first record
     * @return object
     */
    public static function first()
    {
        return (new static)->firstQuery();
    }

    protected function firstQuery()
    {
        return Database::table($this->table)->first();
    }

    /**
     * Create new model object
     * @param $data
     * @return object
     */
    public static function create($data)
    {
        return (new static)->insertQuery($data);
    }

    protected function insertQuery($data)
    {
        return Database::table($this->table)->insert($data);
    }

    /**
     * Update record on given table
     * @param $data
     * @return boolean
     */
    public
    static function update($data)
    {
        return (new static)->updateQuery($data);
    }

    protected
    function updateQuery($data)
    {
        return Database::table($this->table)->update($data);
    }

    /**
     * Delete record
     * @return boolean
     */
    public
    static function delete()
    {
        return (new static)->deleteQuery();
    }

    protected
    function deleteQuery()
    {
        return Database::table($this->table)->delete();
    }

    public function __call($name, $arguments)
    {
        if ($name == 'save') {
            return $this->insertQuery($this->attributes);
        }

        die('Error: call to unsupported method: ' . $name);
    }
}
