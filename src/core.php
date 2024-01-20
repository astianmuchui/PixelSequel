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
    public static function All(mixed $table, mixed $order_by="", mixed $order=""): array | object;
    public static function Find(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "",string $order=""): array;
    public static function Search(mixed $table, mixed $param_t="id", mixed $param_n, mixed $order_by = "", mixed $order=""): array;
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
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @param array $data: data to be inserted
    */

    public static function Update(mixed $table, mixed $param_t = "id", mixed $param_n, array $data): bool
    {
        /**
          * Update $table SET $data WHERE $param_t = $param_n
        */
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
     * @All: get all records from table
     * @param string $table: table name
     * @return array
     */

    public static function All(mixed $table, mixed $order_by="", mixed $order=""): array | object
    {
        if ($order_by == "")
        {
            $sql = "SELECT * FROM `$table`";
        }
        else
        {
            $sql = "SELECT * FROM `$table` ORDER BY `$order_by` $order";
        }

        $stmt = self::$connection->query($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @FetchN: fetch N records from table
     * @param string $table: table name
     * @param string $param_t: parameter type
     * @param string $param_n: parameter name
     * @param int $limit: limit
     * @return array
    */

    public function FetchN(mixed $table, mixed $param_t="id", mixed $param_n, int $limit, mixed $order_by="" , string $order=""): array | object
    {
        if ($order_by == "")
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` = $param_n LIMIT $limit";
        }
        else
        {
            $sql = "SELECT * FROM `$table` WHERE `$param_t` = $param_n ORDER BY `$order_by` $order LIMIT $limit";
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


?>
