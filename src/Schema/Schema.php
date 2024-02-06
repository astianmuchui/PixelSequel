<?php

namespace PixelSequel\Schema;



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
use PDO, PDOException;


/** Enable debug: Remove this in production */
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);


class CipherOps
{
   public static $method = "AES-128-CTR";
   public static $options = 0;
   public static $enc_iv = '1234567891011121';

   public static $key = '$2y$10$Lvh7toMVlSJjwmMHSZ5ULOWkFITbUuK6mr/NG2YKluolXTpI.lLbu';
   public static $pepper = '$2y$10$np7bVhRUeR5qQNDlAL.hOOvDaEwZdghmLpz8HjkVJnX0vJbmuyto2';
   public static $salt = '$2y$10$PYbF/lbCcZ5G4wK39svrRO0k2HM/rj.Iu8NqUxpcI01BmfIZq0J9e';

   public static function aes_ctr_ssl_encrypt128( string | array | int $data)
   {
      $method = self::$method;
      $enc_key = self::$key;
      $options = self::$options;
      $enc_iv = self::$enc_iv;
      $iv_length = openssl_cipher_iv_length($method);

      switch(gettype($data))
      {
         case "Array":
            return openssl_encrypt($data,$method,$enc_key,$options,$enc_iv);
         case "Integer":
            return openssl_encrypt($data,$method,$enc_key,$options,$enc_iv);

         case "string":
            return openssl_encrypt($data,$method,$enc_key,$options,$enc_iv);
      }
   }
   public static function aes_ctr_ssl_decrypt128( string | array | int $data)
   {
      $method = self::$method;
      $enc_key = self::$key;
      $options = self::$options;
      $enc_iv = self::$enc_iv;

      switch(gettype($data))
      {
         case "Array":
            return openssl_decrypt($data,$method,$enc_key,$options,$enc_iv);
         case "Integer":
            return openssl_decrypt($data,$method,$enc_key,$options,$enc_iv);
         case "string":
            return openssl_decrypt($data,$method,$enc_key,$options,$enc_iv);
      }
   }
}

class Connector
{

    public $uname;
    public $user;
    public $pwd;
    public $host;
    public $conn;
    public $db;

    public static $connection;
    public static $Connected;
    /**
     * @__construct: constructor function
     * @param void
     * @return void
    */

    public function __construct()
    {
        $_SESSION['uname'] = CipherOps::aes_ctr_ssl_decrypt128($_SESSION['uname']);
        $_SESSION['pwd']   = CipherOps::aes_ctr_ssl_decrypt128($_SESSION['pwd']);
        $_SESSION['host']  = CipherOps::aes_ctr_ssl_decrypt128($_SESSION['host']);
        $_SESSION['db']    = CipherOps::aes_ctr_ssl_decrypt128($_SESSION['db']);


        echo $_SESSION['db'];
        $this->uname = $_SESSION['uname'];
        $this->pwd   = $_SESSION['pwd'];
        $this->host  = $_SESSION['host'];
        $this->db    = $_SESSION['db'];

        self::$connection = $this->connect();

        if (self::$connection instanceof PDO)
        {
            self::$Connected = true;
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

            return (self::$connection) ?: false;
        }
        catch (PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
            return false;
        }
    }

    public function query(string $sql)
    {
        $stmt = $this->conn->query($sql);
        return $stmt;
    }
    public function __destruct()
    {
        self::$connection = null;
        self::$Connected = false;
    }

}




interface PixelSequelSchema
{
    public static function Exists(string $table): bool;
    public static function Create(mixed $table, array $data): bool;
    public static function Drop(mixed $table): bool;
    public static function Alter(string $table, mixed $column, mixed $set): bool;
}

class Schema extends Connector implements PixelSequelSchema
{
    public $conn;
    /**
     * @__construct: constructor function
     * @param void
     * @return void
    */

    public function __construct()
    {
        parent::__construct();
        $this->conn = self::$connection;
    }

    /**
     * @Exists: check if table exists
     * @param string $table: table name
     * @return bool
     */

     public static function Exists(string $table): bool
     {
        echo $_SESSION['uname'];
        echo $_SESSION['pwd'];
        echo $_SESSION['host'];
         $sql = "SHOW TABLES LIKE '$table'";
         $stmt = self::$connection->query($sql);
         $stmt->execute(null);
         return $stmt->rowCount() > 0;
     }

    /**
    * @Create: create table
    * @param mixed $table: table name
    * @param array $data: nested array of columns with their properties
    * @return bool
    */


    public static function Create(mixed $table, array $data = [[]]): bool
    {

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

        $stmt = self::$connection->query($sql);
        $stmt->execute();

        /** Alter tables to add primary keys if they do exist */
        if ($primaryKey)
        {
            $alterSql = "ALTER TABLE `$table` ADD PRIMARY KEY (`$primaryKey`);";
            $alterStmt = self::$connection->query($alterSql);
            $alterStmt->execute();
        }

        return true;
    }


    /**
     * @Drop: drops table
     * @param string $table: table name
     * @return bool
    */

    public static function Drop(mixed $table): bool
    {
        $sql = "DROP TABLE `$table`";
        $stmt = self::$connection->query($sql);
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



        $sql = "ALTER TABLE `$table`  \n \t MODIFY `$column` $set;";

        $stmt = self::$connection->query($sql);
        return $stmt->execute();
    }

}

?>

