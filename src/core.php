<?php



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
*/
namespace PixelSequel;

/** Enable debug: Remove this in production */
ini_set('display_errors', 'On');
error_reporting(E_ALL);

use PDO, PDOException, PDOStatement;

interface PixelSequelORM
{

}

class Model implements PixelSequelORM
{

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

    public function __construct($uname = "root", $pwd = "", $host = "localhost", $db)
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

    public function query($sql): PDOStatement
    {
        return $this->conn->query($sql);
    }

    /**
     * @All: get all records from table
     * @param string $table: table name
     * @return array
     */

    public static function All($table): array | object
    {
        $sql = "SELECT * FROM $table";
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

    public static function Find($table, $param_t="id", $param_n): array
    {
        $sql = "SELECT * FROM `$table` WHERE `$param_t` = $param_n";
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

    public static function Search($table, $param_t="id", $param_n): array
    {
        $sql = "SELECT * FROM `$table` WHERE `$param_t` LIKE '%$param_n%' ";
        $stmt = self::$connection->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @Create: create record from table
     * @param string $table: table name
     * @param array $data: data to be inserted
     * @return bool
    */

    public function disconnect(): void
    {
        unset($this->conn);
        self::$connection = null;
        self::$Connected = false;
    }
}

/**
 * @function test_ORM: test function
 * @param void
 * @return void
 */

function test_ORM()
{
    $session = new Model (
        uname: "root",
        pwd: "",
        host:"localhost",
        db:"pixel_sequel"
    );

    var_dump(Model::All(table: "users"));
    var_dump(Model::Find(table: "users", param_t: "id",param_n: 24));
    var_dump(Model::Search(table: "users", param_t: "uname",param_n: "seb"));

    $session->disconnect();
}

test_ORM();
