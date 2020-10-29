<?php

namespace Smll\Database;

use PDO;
use Smll\File\File;
use Smll\Http\Request;
use Smll\Url\Url;

class Database
{
    /**
     * Database instance
     * @var string
     */
    protected static $instance;

    /**
     * Select data
     * @var string
     */
    protected static $select;

    /**
     * Table name
     * @var string
     */
    protected static $table;

    /**
     * Join data
     * @var string
     */
    protected static $join;

    /**
     * Where data
     * @var string
     */
    protected static $where;

    /**
     * Where binding
     * @var array
     */
    protected static $whereBinding = [];

    /**
     * Group by data
     * @var string
     */
    protected static $groupBy;

    /**
     * Having data
     * @var string
     */
    protected static $having;

    /**
     * Having binding
     * @var array
     */
    protected static $havingBinding = [];

    /**
     * Order by data
     * @var string
     */
    protected static $orderBy;

    /**
     * Limit data
     * @var string
     */
    protected static $limit;

    /**
     * Offset data
     * @var string
     */
    protected static $offset;

    /**
     * Query
     * @var string
     */
    protected static $query;

    /**
     * Setter
     * @var mixed
     */
    protected static $setter;

    /**
     * All binding
     * @var array
     */
    protected static $binding = [];

    /**
     * Database connection
     * @var $connection
     */
    protected static $connection;

    public function __construct()
    {
    }

    private static function connect()
    {
        if (!static::$connection) {
            $database_data = File::require_file('/config/database.php');
            extract($database_data);
            $dsn = "mysql:dbname=$DATABASE;host=$HOST";
            $options = [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            try {
                static::$connection = new PDO($dsn, $USERNAME, $PASSWORD, $options);
            } catch (\PDOException $exception) {
                throw new \Exception($exception);
            }
        }
    }

    /**
     * Get the instance of class
     * @return Database
     * @throws \Exception
     */
    private static function instance()
    {
        static::connect();
        if (!static::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Database query
     * @param null $query
     * @return Database|string
     * @throws \Exception
     */
    public static function query($query = null)
    {
        static::instance();

        if ($query == null) {
            if (!static::$table) {
                throw new \Exception("Unknown table.");
            }
            $query = "SELECT ";
            $query .= static::$select ?: "*";
            $query .= " FROM " . static::$table . " ";
            $query .= static::$join . " ";
            $query .= static::$where . " ";
            $query .= static::$groupBy . " ";
            $query .= static::$having . " ";
            $query .= static::$orderBy . " ";
            $query .= static::$limit . " ";
            $query .= static::$offset . " ";
        }

        static::$query = $query;
        static::$binding = array_merge(static::$whereBinding, static::$havingBinding);

        return static::instance();
    }

    /**
     * Select data from table
     * @return Database|string
     */
    public static function select()
    {
        $select = func_get_args();
        $select = implode(', ', $select);

        static::$select = $select;

        return static::instance();
    }

    /**
     * Define table
     * @param $table
     * @return Database|string
     */
    public static function table($table)
    {
        static::$table = $table;

        return static::instance();
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
        static::$join .= " " . $type . " JOIN " . $table . " ON " . $first . $operator . $second . " ";

        return static::instance();
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
        static::join($table, $first, $operator, $second, "RIGHT");

        return static::instance();
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
        static::join($table, $first, $operator, $second, "LEFT");

        return static::instance();
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
        $where = "`" . $column . "`" . $operator . " ? ";

        if (!static::$where) {
            $statement = " WHERE " . $where;
        } else {
            if ($type == null) {
                $statement = " AND " . $where;
            } else {
                $statement = " " . $type . " " . $where;
            }
        }

        static::$where .= $statement;
        static::$whereBinding[] = htmlspecialchars($value);

        return static::instance();
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
        static::where($column, $operator, $value, "OR");

        return static::instance();
    }

    /**
     * Group by data
     * @return object
     */
    public static function groupBy()
    {
        $group_by = func_get_args();
        $group_by = "GROUP BY " . implode(', ', $group_by) . " ";

        static::$groupBy = $group_by;

        return static::instance();
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
        $having = "`" . $column . "`" . $operator . " ? ";

        if (!static::$having) {
            $statement = " HAVING " . $having;
        } else {
            $statement = " AND " . $having;
        }


        static::$having .= $statement;
        static::$havingBinding[] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * order by data
     * @param string $column
     * @param string $type
     * @return object
     */
    public static function orderBy(string $column, string $type = null)
    {
        $sep = static::$orderBy ? " , " : "ORDER BY ";
        $type = strtoupper($type);

        $type = ($type != null && in_array($type, ['ASC', 'DESC'])) ? $type : "ASC";
        $statement = $sep . $column . " " . $type . " ";

        static::$orderBy .= $statement;
        return static::instance();
    }

    /**
     * Limit
     * @param $limit
     * @return Database|string
     */
    public static function limit($limit)
    {
        static::$limit = "LIMIT " . $limit . " ";

        return static::instance();
    }

    /**
     * Offset
     * @param $offset
     * @return Database|string
     */
    public static function offset($offset)
    {
        static::$offset = "OFFSET " . $offset . " ";

        return static::instance();
    }

    /**
     * Fetch execute
     * @return object
     */
    private static function fetchExecute()
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();

        return $data;
    }

    /**
     * Get records
     * @return object
     */
    public static function get()
    {
        $data = static::fetchExecute();
        $result = $data->fetchAll();

        return $result;
    }

    /**
     * Get record
     * @return object
     */
    public static function first()
    {
        $data = static::fetchExecute();
        $result = $data->fetch();

        return $result;
    }

    /**
     * Execute
     * @param array $data
     * @param string $query
     * @param string|null $where
     */
    private static function execute(array $data, string $query, string $where = null)
    {
        static::instance();

        if (!static::$table) {
            throw new \Exception('Unknown table');
        }
        foreach ($data as $key => $value) {
            static::$setter .= '`' . $key . '` = ?, ';
            static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
        }
        static::$setter = trim(static::$setter, ', ');


        $query .= static::$setter;
        $query .= $where != null ? static::$where . " " : '';

        static::$binding = $where != null ? array_merge(static::$binding, static::$whereBinding) : static::$binding;

        $data = static::$connection->prepare($query);

        $data->execute(static::$binding);

        static::clear();
    }

    /**
     * Insert data to given table
     * @param $data
     * @return object
     */
    public static function insert($data)
    {
        $table = static::$table;
        $query = "INSERT INTO " . $table . " SET ";
        static::execute($data, $query);

        $object_ID = static::$connection->lastInsertId();
        $object = static::table($table)->where('id', '=', $object_ID)->first();

        return $object;
    }

    /**
     * Update record on given table
     * @param $data
     * @return boolean
     */
    public static function update($data)
    {
        $query = "UPDATE " . static::$table . " SET ";
        static::execute($data, $query, true);


    }

    /**
     * Delete record
     * @return boolean
     */
    public static function delete()
    {
        $query = "DELETE FROM " . static::$table;
        static::execute([], $query, true);

        return true;
    }

    /**
     * Pagination
     * @param int $per_page
     * @return array
     */
    public static function paginate($per_page = 20)
    {
        static::$paginate = $per_page;
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute();

        $total_pages = ceil($data->rowCount() / $per_page);
        $page = Request::get('page');
        $current_page = (!is_numeric($page) || $page < 1) ? "1" : $page;
        $offset = ($current_page - 1) * $per_page;
        static::limit($per_page);
        static::offset($offset);
        static::query();

        $data = static::fetchExecute();
        $result = $data->fetchAll();

        return [
            'data' => $result,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
        ];
    }

    /**
     * links for pagination
     * @param $current_page
     * @param $total_pages
     * @return string
     */
    public static function links($current_page, $total_pages)
    {
        $links = '';
        $from = $current_page - 2;
        $to = $current_page + 2;

        if ($from < 2) {
            $from = 2;
            $to = $from + 4;
        }

        if ($to >= $total_pages) {
            $diff = $to - $total_pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $total_pages - 1;
        }

        if ($from < 2) {
            $from = 1;
        }

        if ($to >= $total_pages) {
            $to = $total_pages - 1;
        }

        if ($total_pages > 1) {
            $links .= '<ul class="pagination"></ul>';
            $full_link = Url::path(Request::full_url());
            $full_link = preg_replace('/\?page=(.*?)/', '', $full_link);
            $full_link = preg_replace('/&page=(.*?)/', '', $full_link);

            $current_page_active = $current_page == 1 ? 'active' : '';
            $href = strpos($full_link, '?') ? ($full_link . '&page=1') : ($full_link . '?page=1');
            $links .= "<li class='link $current_page_active'><a href='$href'>First</a></li>";

            for ($i = $from; $i <= $to; $i++) {
                $current_page_active = $current_page == $i ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link . '&page='.$i) : ($full_link . '?page='.$i);
                $links .= "<li class='link $current_page_active'><a href='$href'>$i</a></li>";
            }

            if ($total_pages > 1) {
                $current_page_active = $current_page == $total_pages ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link . '&page=' . $total_pages) : ($full_link . '?page=' . $total_pages);
                $links .= "<li class='link $current_page_active'><a href='$href'>Last</a></li>";
            }

            return $links;
        }
    }

    /**
     * clear all properties
     * @return void
     */
    private static function clear()
    {
        static::$select = '';
        static::$join = '';
        static::$where = '';
        static::$whereBinding = [];
        static::$having = '';
        static::$havingBinding = [];
        static::$groupBy = '';
        static::$orderBy = '';
        static::$limit = '';
        static::$offset = '';
        static::$binding = [];
        static::$query = '';
        static::$table = '';
        static::$instance = '';
    }

    /**
     * Get query
     * @return string
     */
    public static function getQuery()
    {
        static::query(static::$query);
        return static::$query;
    }
}
