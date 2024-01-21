<?php

require 'core.php';

use PixelSequel\Schema as Table;
use PixelSequel\Model as Database;

new Database (
    uname: "root",
    pwd: "",
    host: "localhost",
    db: "pixel_sequel"
);

Table::Create (
    table: "some_weird_stuff",
    data: [
        "id" => [
            "type" => "int",
            "length" => 11,
            "auto_increment" => true,
            "primary_key" => true
        ],
        "uname" => [
            "type" => "varchar",
            "length" => 255,
            "null" => false
        ]
    ]
);

Table::Alter (
    table: "some_weird_stuff",
    column: "id",
    set: "int(255) NOT NULL AUTO_INCREMENT"
);


Table::Drop (
    table: "some_weird_stuff"
);

?>

