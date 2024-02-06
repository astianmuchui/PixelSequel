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


/** Enable debug: Remove this in production */
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);



require "../src/core.php";
use PixelSequel\Model;
use PDO, PDOException;

interface PixelSequelSchema
{
    public static function Exists(string $table): bool;
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
        if (!(Model::$connection instanceof PDO))
        {
            die ("Error: Database not connected");
        }
    }


    /**
     * @TableExists: check if table exists
     * @param string $table: table name
     * @return bool
     */

     public static function Exists(string $table): bool
     {
         $sql = "SHOW TABLES LIKE '$table'";
         $stmt = Model::$connection->query($sql);
         $stmt->execute();
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

        if (self::Exists($table))
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
     * @Drop: drops table
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

