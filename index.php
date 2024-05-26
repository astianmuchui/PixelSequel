<?php

/** Basic How-to file */

require_once 'src/Model/Model.php';
require_once 'src/Schema/Schema.php';


use PixelSequel\Model\Model;
use PixelSequel\Schema\Schema;

new Model (
    dbname: 'pixel_sequel',
    username: 'root',
    password: '',
);

if (Model::Connected())
{
    new Schema(Model::Connection());
}
else
{
    echo "Not connected";
}



Schema::Create (
    table: 'pxtest',
    structure: [
        "id" => [
            "type" => "int",
            "length" => 11,
            "primary_key" => true
        ],

        "username" => [
            "type" => "varchar",
            "length" => 11,
            "null" => false
        ]

    ]
);


var_dump (
    Model::All (
        table: 'users'
    )
);

