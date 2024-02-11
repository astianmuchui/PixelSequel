# PixelSequel ORM


[![wakatime](https://wakatime.com/badge/user/5a50e193-2e98-47bd-9b67-0952bed984cf/project/018cf232-1343-4d6a-8c07-06c12563a3e6.svg)](https://wakatime.com/badge/user/5a50e193-2e98-47bd-9b67-0952bed984cf/project/018cf232-1343-4d6a-8c07-06c12563a3e6)

# Introduction

PixelSequel ORM is a versatile and lightweight Object-Relational Mapping (ORM) solution for PHP. This comprehensive ORM framework seamlessly integrates with PHP applications, providing a powerful set of tools for database connectivity, schema management, and data manipulation. The project comprises two fundamental components: the Model, offering an intuitive interface for executing common database operations such as insertion, updating, querying, and deletion; and the Schema, empowering developers to effortlessly define, create, and modify database structures. With an emphasis on simplicity and efficiency, PixelSequel ORM serves as a valuable resource for PHP developers seeking a reliable ORM solution for their projects.


## Author
```Sebastian Muchui```

## License
```MIT```

## Version
```0.1.0```


# Installation

You need to have PHP installed in your machine to use this package. If you do not have PHP installed, you can download it from the [official PHP website](https://www.php.net/downloads.php).

Secondly, ensure you have composer installed in your machine. If not, you can install it by following the instructions provided in the [official composer website](https://getcomposer.org/download/).
Once this is done, you need to create a composer.json file in your project root directory and add the following code:

```json
    {
        "minimum-stability" : "dev"
    }
```

After that, you can install the package by running the following command in your terminal:

```bash
    composer require astianmuchui/pixelsequel
```

# Usage
The ORM comes with 2 major parts, Built seperately to allow for flexibility and ease of use. The two parts are:

- The Model
- The Schema

## The Model
The model is the part of the ORM that allows you to interact with the database tables. It allows you to perform operations such as insertion, updating, querying, and deletion. To use the model, you need to create a new instance of the model class.

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel',
        username: 'root',
        password: '',
        dbhost: 'localhost'
    );
```

By Default, the following values are set:
- ```username```: ```'root'```
- ```password```: ```''```
- ```dbhost```: ```'localhost'```

In a development environment, you can just pass the dbname as the only parameter to the model class.

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel'
    );
```

### Inserting Data

To insert data, you just need to call the ```Insert``` static method. Note that the Model instance does not need to be assigned to a variable
as the methods will infer the connection from the instantiated object.

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel'
    );

    Model::Insert(

        table: 'users',

        data: [
            'name' => 'John Doe',
            'email' => 'john@gmail.com'
        ]
    );
```

The keys of the data arrays must be valid column names in the table. The corresponding values are the values to be inserted into the table.
In upcoming versions, the ORM will be able to automatically infer the column names from the table and validate the data before insertion.

### Reading Data

Here you basically need to just call the ```All``` method. The method can take a variety of parameters to filter the data you want to retrieve.

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel'
    );

    $users = Model::All(

        table: 'users',

        where: [
            'name' => 'John Doe'
        ]

        where_like: [
            'email' => 'gmail'
        ]

        order_by: 'id',
        order: 'DESC',

        limit: 10
    );
```

- All the parameters are:

```
    table: The table to read from
    where: An associative array of the column names and their values to filter the data using the where clause
    where_like: An associative array of the column names and their values to perform searches
    order_by: The column to order the data by
    order: The order to use. Either 'ASC' or 'DESC'
    limit: The number of records to retrieve
    json: A boolean value to determine if the data should be returned as a json string or an array
```

The method returns an object of the data retrieved. You can then loop through the object to get the data.
If you set the limit to 1, you would need to select the zeroth index of the object to get the data.
Typically one of the key goals while developing this was to make it that a single function does all the select functionality.
There are other select methods but ignore them as they are not well written and will be removed in future versions.

### Updating Data
The update method is basically a replica of the Insert method, However the parameter type to identify the row and its value must be passed in

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel'
    );

    Model::Update(

        table: 'users',
        param_t: 'id'
        param_n: 1,
        data: [
            'name' => 'John Doe',
            'email' => 'john@gmail.com'
        ]
    );
```

```param_t``` is the parameter type, typically a column name. ```param_n``` is the value of the parameter type.

### Deleting Data

The ```Delete``` method is used to delete data from the database. It takes in the table name and the parameter type and value to identify the row to delete.

```php
    use PixelSequel\Model\Model;

    new Model (
        dbname: 'pixel_sequel'
    );

    Model::Delete(

        table: 'users',
        param_t: 'id'
        param_n: 1
    );
```

## The Schema
The schema is the part of the ORM that allows you to interact with the database tables. It allows you to perform operations such as creating, updating, and deleting tables. To use the schema, you need to create an instance of the model class and pass the database connection to the schema class. Note that the Model instance does not need to be assigned to a variable and the schema as well

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;
# Features

    new Model (
        dbname: 'pixel_sequel'
    );

    new Schema (
        Model::Connection()
    );
```

The connection can be checked by calling the ```Connected``` method on the model class. The method returns a boolean value.

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;

    new Model (
        dbname: 'pixel_sequel'
    );



    if (Model::Connected())
    {
        new Schema (
          Model::Connection()
        );

        echo 'Connected';
    }
    else
    {
        echo 'Not Connected';
    }
```

### Creating Tables
Once the connection is established, you can create tables by calling the ```Create``` method on the schema class. The method takes in the table name and an associative array of the column names and their data types.

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;

    new Model (
        dbname: 'pixel_sequel'
    );

    new Schema (
        Model::Connection()
    );

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
```

The keys of the structure array must be valid column names in the table. The corresponding values are associative arrays of the column data types and their properties. The properties are:

```
    type: The data type of the column
    length: The length of the column
    primary_key: A boolean value to determine if the column is a primary key
    null: A boolean value to determine if the column can be null
    auto_increment: A boolean value to determine if the column is auto increment
```

### Altering Tables

The ```Alter``` method is used to alter tables. It takes in three parameters: the table name, the column name, and what to set

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;

    new Model (
        dbname: 'pixel_sequel'
    );

    new Schema (
        Model::Connection()
    );

    Schema::Alter (
        table: 'pxtest',
        column: 'username'
        set: "int(255) NOT NULL"
    );
```

### Dropping Tables
The ```Drop``` method is used to drop tables. It takes in the table name as a parameter.

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;

    new Model (
        dbname: 'pixel_sequel'
    );

    new Schema (
        Model::Connection()
    );

    Schema::Drop (
        table: 'pxtest'
    );
```

To disconnect from the database, you can call the ```Disconnect``` method on the model class.

```php
    use PixelSequel\Model\Model;
    use PixelSequel\Schema\Schema;

    new Model (
        dbname: 'pixel_sequel'
    );

    new Schema (
        Model::Connection()
    );

    Model::Disconnect();
```

# Upcoming Features
- Validation of data before insertion
- Automatic inference of column names from the table
- More robust select methods
- More robust update methods
- More robust delete methods
- More robust create methods
- Elimination of the key value syntax


# Contributions
Contributions are welcome. You can contribute by forking the repository and making a pull request. You can also open an issue if you find any bugs or have any feature requests.

### Built with ❤️ by ```Sebastian Muchui```