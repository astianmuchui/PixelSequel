<?php

/** Everything gets tested here */

require "core.php";

use PixelSequel\Model as Database;

new Database (
    uname: "root",
    pwd: "",
    host:"localhost",
    db:"pixel_sequel"
);

// Insert data
Database::Insert (
    table: "users",
    data: [
    "uname" => "Cody",
    "email" => "rhodes@gmail.com",
    "pwd" => "123456",
    "phone" => "0756894230"
]);

// Update data
Database::Update (
    table: "users",
    param_t: "id",
    param_n: "10",

    data: [
    "uname" => "Cody Rhodes",
    "email" => "codyrhodes@gmail.com"
]);

// Fetch data with parameters
var_dump (
    Database::All (
        table: "users",
        order_by: "id",
        order: "ASC",
        where: [
            "uname" => "Cody"
        ],
        limit: 1,
        json : true
    )
);

// Search Data

var_dump (
    Database::All (
        table: "users",
        order_by: "id",
        order: "ASC",
        where_like: [
            "phone" => "0794"
        ],
        limit: 4
    )
);


// Delete data
Database::Delete (
    table: "users",
    param_t: "uname",
    param_n: "seb"
);

Database::Disconnect();

?>
