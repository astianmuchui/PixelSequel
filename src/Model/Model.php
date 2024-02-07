<?php


namespace PixelSequel\Model;


/**
*
* @package  PixelSequel ORM
* @description  PixelSequel ORM is a lightweight ORM for PHP
* @version  1.0.0
* @since  1.0.0
* @license  None
* @author Sebastian Muchui
* @subpackage Model
* @category ORMs
*
*/


/** Enable debug: Remove this in production */
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);



use PDO, PDOException, PDOStatement;

interface PixelSequelORM
{
    public function connect(): PDO | bool;
    public static function Connection(): PDO;
    public static function Connected(): bool;
    public static function Insert(mixed $table, array $data): bool;
    public static function Update(mixed $table,  mixed $param_n, array $data, mixed $param_t = "id"): bool;
    public static function All(mixed $table, iterable $where=null, iterable $where_like=null, mixed $order_by="", mixed $order="", int $limit=null, bool $json=false): mixed;
    public static function Find(mixed $table, mixed $param_n, mixed $param_t="id", mixed $order_by = "",string $order=""): array;
    public static function Search(mixed $table,  mixed $param_n, mixed $param_t="id", mixed $order_by = "", mixed $order=""): array;
    public static function Delete(mixed $table,  mixed $param_n, mixed $param_t="id",): bool;
    public static function DeleteAll(mixed $table): bool;
    public static function Disconnect(): void;
}

class Model implements PixelSequelORM
{

    /**
    * @property uname: username
    * @property pwd: password
    * @property host: host
    * @property conn: connection
    * @property db: database
    * @property Connected: connection status
    * @property connection: connection object
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

    public function __construct( mixed $dbname , mixed $username = "root", mixed $password = "", mixed $dbhost = "localhost" )
    {
        $this->uname = $username;
        $this->pwd = $password;
        $this->host = $dbhost;
        $this->db = $dbname;


        if (!(self::$Connected instanceof true))
        {
            $this->connect();
        }
    }

    /**static
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
            $_SESSION['status'] = self::$Connected = true;


            return ($this->conn) || false;
        }
        catch (PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    /**
     * @Connection: get connection
     * @param void
     * @return PDO
     */

    public static function Connection(): PDO
    {
        return self::$connection;
    }

    /**
     * @Connected: check if connected
     * @param void
     * @return bool
     */

    public static function Connected(): bool
    {
        return self::$Connected;
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

    public static function Update(mixed $table,  mixed $param_n, array $data, mixed $param_t = "id"): bool
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

    public static function Find(mixed $table, mixed $param_n, mixed $param_t="id", mixed $order_by = "",string $order=""): array
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

    public static function Search(mixed $table,  mixed $param_n, mixed $param_t="id", mixed $order_by = "", mixed $order=""): array
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
     * @Delete: delete record from table
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @return bool
    */

    public static function Delete(mixed $table,  mixed $param_n, mixed $param_t="id",): bool
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




?>
