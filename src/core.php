<?php

namespace PixelSequel;


/**
*
* @package  PixelSequel ORM
* @description  PixelSequel ORM is a lightweight ORM for PHP
* @version  1.0.0
* @since  1.0.0
* @license  None
* @author Sebastian Muchui
* @subpackage core
* @category ORMs
*
*/


/** Enable debug: Remove this in production */
ini_set('display_errors', 'On');
error_reporting(E_ALL);

use PDO, PDOException, PDOStatement;

interface PixelSequelORM
{
    public function connect(): PDO | bool;
    public function query(mixed $sql): PDOStatement;
    public static function Insert(mixed $table, array $data): bool;
    public static function Update(mixed $table, mixed $param_t = "id", mixed $param_n, array $data): bool;
    public static function All(mixed $table, iterable $where=null, iterable $where_like=null, mixed $order_by="", mixed $order="", int $limit=null, bool $json=false): mixed;
    public static function Find(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "",string $order=""): array;
    public static function Search(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "", mixed $order=""): array;
    public static function TableExists(string $table): bool;
    public static function Delete(mixed $table, mixed $param_t="id", mixed $param_n): bool;
    public static function DeleteAll(mixed $table): bool;
    public static function Disconnect(): void;

}

class Model implements PixelSequelORM
{

    /**
     * @uname: username
     * @pwd: password
     * @host: host
     * @conn: connection
     * @db: database
     * @Connected: connection status
     * @connection: connection object
    */

    public $uname;
    public $user;
    public $pwd;
    public $host;
    public $conn;
    public $db;
    public static $Connected;
    public static $connection;

    /**
     * @__construct: constructor function
     * @param string $uname: username
     * @param string $pwd: password
     * @param string $host: host
     * @param string $db: database
     * @defaults: 
    */

    public function __construct(mixed $uname = "root", mixed $pwd = "", mixed $host = "localhost", mixed $db)
    {
        $this->uname = $uname;
        $this->pwd = $pwd;
        $this->host = $host;
        $this->db = $db;

        if (!(self::$Connected instanceof true))
        {
            $this->connect();
        }
    }

    /**
     * @connect: connect to database
     * @param void
     * @return PDO | bool
    */

    public function connect(): PDO | bool
    {
        try
        {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->uname, $this->pwd);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::$connection = $this->conn;
            self::$Connected = true;

            return ($this->conn) || false;
        }
        catch (PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    /**
     * @query: query database
     * @param string $sql: sql query
     * @return PDOStatement
    */

    public function query(mixed $sql): PDOStatement
    {
        return $this->conn->query($sql);
    }

    /**
     * @Insert: insert record into table
     * @param string $table: table name
     * @param array $data: data to be inserted
     */

    public static function Insert(mixed $table, array $data): bool
    {
        $sql = "INSERT INTO `$table` (";
        $sql .= implode(", ", array_keys($data)) . ') VALUES (';
        $sql .= ":" . implode(", :", array_keys($data)) . ')';

        $stmt = self::$connection->prepare($sql);

        foreach ($data as $key => $value)
        {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }
    /**
     * @Update: update record from table
     * @param mixed $table: table name
     * @param mixed $param_t: parameter type
     * @param mixed $param_n: parameter name
     * @param array $data: data to be inserted
    */

    public static function Update(mixed $table, mixed $param_t = "id", mixed $param_n, array $data): bool
    {

    /**
     * @Create: create record from table
     * @param string $table: table name
     * @param array $data: data to be inserted
     * @return bool
    */

        $sql = "UPDATE `$table` SET ";
        $sql .= implode(" = ?, ", array_keys($data)) . " = ? ";
        $sql .= "WHERE `$param_t` = '$param_n'";
        $stmt = self::$connection->prepare($sql);
        $stmt->execute(array_values($data));
        return (true);

    }


    /**
     * @All - Selects all records from table
     * @param string $table: table name
     * @param array $where: where clause
     * @param array $where_like: where like clause
     * @param string $order_by: order by clause
     * @param string $order: order clause
     * @param int $limit: limit clause
     * @return array
     */

    public static function All(mixed $table, iterable $where=null, iterable $where_like=null, mixed $order_by="", mixed $order="", int $limit=null, bool $json=false): mixed
    {
        if ($where == null && $where_like == null)
        {
            if ($order_by == "")
            {
                $sql = "SELECT * FROM `$table`";
            }
            else
            {
                $sql = "SELECT * FROM `$table` ORDER BY `$order_by` $order";
            }
        }
        else if ($where != null && $where_like == null)
        {
            $sql = "SELECT * FROM `$table` WHERE ";
            foreach ($where as $key => $value)
            {
                $sql .= "`$key` = '$value' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }
        else if ($where == null && $where_like != null)
        {
            $sql = "SELECT * FROM `$table` WHERE ";
            foreach ($where_like as $key => $value)
            {
                $sql .= "`$key` LIKE '%$value%' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }
        else if ($where != null && $where_like != null)
        {
            $sql = "SELECT * FROM `$table` WHERE ";
            foreach ($where as $key => $value)
            {
                $sql .= "`$key` = '$value' AND ";
            }
            foreach ($where_like as $key => $value)
            {
                $sql .= "`$key` LIKE '%$value%' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }

        if ($limit != null)
        {
            $sql .= " LIMIT $limit";
        }

        $stmt = self::$connection->query($sql);
        $stmt->execute();

        if ($json = true)
        {
            return json_encode($stmt->fetchAll());
        }
        else
        {
            return $stmt->fetchAll();
        }
    }

    /**
     * @Select: fetch N records from table
     * @param array rows: rows
     * @param mixed table: table name
     * @param mixed order_by: order by
     * @param mixed order: order
     * @param where: where
     * @param where_like: where like
     * @param int $limit: limit
     * @return array or object
    */

    public function Select(mixed $table, array $rows,mixed $order_by="", mixed $order, mixed $where=null, mixed $where_like=null, int $limit=null): array | object
    {

        if ($where == null && $where_like == null)
        {
            if ($order_by == "")
            {
                $sql = "SELECT ";
                $sql .= implode(", ", $rows) . " FROM `$table`";
            }
            else
            {
                $sql = "SELECT ";
                $sql .= implode(", ", $rows) . " FROM `$table` ORDER BY `$order_by` $order";
            }
        }
        else if ($where != null && $where_like == null)
        {
            $sql = "SELECT ";
            $sql .= implode(", ", $rows) . " FROM `$table` WHERE ";
            foreach ($where as $key => $value)
            {
                $sql .= "`$key` = '$value' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }
        else if ($where == null && $where_like != null)
        {
            $sql = "SELECT ";
            $sql .= implode(", ", $rows) . " FROM `$table` WHERE ";
            foreach ($where_like as $key => $value)
            {
                $sql .= "`$key` LIKE '%$value%' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }
        else if ($where != null && $where_like != null)
        {
            $sql = "SELECT ";
            $sql .= implode(", ", $rows) . " FROM `$table` WHERE ";
            foreach ($where as $key => $value)
            {
                $sql .= "`$key` = '$value' AND ";
            }
            foreach ($where_like as $key => $value)
            {
                $sql .= "`$key` LIKE '%$value%' AND ";
            }
            $sql = rtrim($sql, "AND ");
            if ($order_by != "")
            {
                $sql .= " ORDER BY `$order_by` $order";
            }
        }

        if ($limit != null)
        {
            $sql .= " LIMIT $limit";
        }

        $stmt = self::$connection->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @Find: find record from table
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @return array
    */

    public static function Find(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "",string $order=""): array
    {
        if ($order_by == "")
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` = $param_n";
        }
        else
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` = $param_n ORDER BY `$order_by` $order";
        }

        $stmt = self::$connection->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @Search: Search record from table
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @return array
    */

    public static function Search(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "", mixed $order=""): array
    {
        if ($order_by == "")
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` LIKE '%$param_n%'";
        }
        else
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` LIKE '%$param_n%' ORDER BY `$order_by` $order";
        }

        $stmt = self::$connection->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @TableExists: check if table exists
     * @param string $table: table name
     * @return bool
     */

     public static function TableExists(string $table): bool
     {
         $sql = "SHOW TABLES LIKE '$table'";
         $stmt = self::$connection->query($sql);
         $stmt->execute();
         return $stmt->rowCount() > 0;
     }

    /**
     * @Delete: delete record from table
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @return bool
    */

    public static function Delete(mixed $table, mixed $param_t="id", mixed $param_n): bool
    {
            $sql = "DELETE FROM `$table` WHERE `$param_t` = '$param_n'";
            $stmt = self::$connection->query($sql);
            $stmt->execute();
            return true;
    }

    /**
     * @DeleteAll: delete all records from table
     * @param string $table: table name
     * @return bool
    */

    public static function DeleteAll(mixed $table): bool
    {
            $sql = "DELETE FROM `$table`";
            $stmt = self::$connection->query($sql);
            $stmt->execute();
            return true;
    }

    /**
     * @Disconnect: disconnect from database
     * @param void
     * @return void
     */

    public static function Disconnect(): void
    {
        self::$connection = null;
        self::$Connected = false;
    }
}

interface PixelSequelSchema
{
    public static function Create(mixed $table, array $data): bool;
    public static function Drop(mixed $table): bool;
    public static function Alter(string $table, mixed $column, mixed $set): bool;
}

class Schema implements PixelSequelSchema
{

    /**
     * @__construct: constructor function
     * @param void
     * @return void
    */

    public function __construct()
    {
        if (!(Model::$Connected instanceof true))
        {
            die ("Error: Database not connected");
        }
    }

    /**
    * @Create: create table
    * @param mixed $table: table name
    * @param array $data: nested array of columns with their properties
    * @return bool
    */


    public static function Create(mixed $table, array $data = [[]]): bool
    {

        if (Model::TableExists($table))
        {
            exit();
        }

        $sql = "CREATE TABLE IF NOT EXISTS `$table` (";

        $primaryKey = (null);

        foreach ($data as $col => $properties)
        {
            if ($col !== array_key_first($data))
            {
                $sql .= ", ";
            }

            $sql .= "`$col`";

            foreach ($properties as $property => $value)
            {
                $property = strtoupper($property);

                switch ($property)
                {
                    case 'TYPE':
                        $sql .= " $value";
                        break;
                    case 'LENGTH':
                        if ($value)
                        {
                            $sql .= "($value)";
                        }
                        break;
                    case 'AUTO_INCREMENT':
                        if ($value)
                        {
                            $primaryKey = $col;
                        }
                        break;
                    case 'DEFAULT':
                        $sql .= " DEFAULT $value";
                        break;
                    case 'NULL':
                        if (!$value)
                        {
                            $sql .= " NOT NULL";
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $stmt = Model::$connection->prepare($sql);
        $stmt->execute();

        /** Alter tables to add primary keys if they do exist */
        if ($primaryKey)
        {
            $alterSql = "ALTER TABLE `$table` ADD PRIMARY KEY (`$primaryKey`);";
            $alterStmt = Model::$connection->prepare($alterSql);
            $alterStmt->execute();
        }

        return true;
    }


    /**
     * @Drop: drop table
     * @param string $table: table name
     * @return bool
    */

    public static function Drop(mixed $table): bool
    {
        $sql = "DROP TABLE `$table`";
        $stmt = Model::$connection->prepare($sql);
        return $stmt->execute();
    }

    /**
     * @Alter: alter table
     * @param string $table: table name
     * @param mixed $column: column to be altered
     * @param mixed $set: what to set
     * @return bool
    */

    public static function Alter(string $table, mixed  $column, mixed $set): bool
    {

        if (!Model::TableExists($table))
        {
            exit();
        }

        $sql = "ALTER TABLE `$table`  \n \t MODIFY `$column` $set;";

        $stmt = Model::$connection->prepare($sql);
        return $stmt->execute();
    }

}

?>
